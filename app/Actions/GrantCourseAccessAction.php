<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EnrollmentStatus;
use App\Mail\CourseAccessGrantedMail;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Libera o acesso aos cursos após a confirmação do pagamento (UC-04, ADR-0006).
 *
 * Idempotente por obrigação: o Asaas entrega PAYMENT_RECEIVED e
 * PAYMENT_CONFIRMED para a mesma cobrança, e reenvia em caso de timeout.
 * Rodar duas vezes não pode gerar matrículas duplicadas nem múltiplos e-mails.
 */
class GrantCourseAccessAction
{
    /**
     * @return Collection<int, Enrollment>
     */
    public function execute(Order $order): Collection
    {
        return DB::transaction(function () use ($order): Collection {
            $order = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            abort_unless($order->grantsAccess(), 409);

            return $this->grant($order);
        });
    }

    /**
     * @return Collection<int, Enrollment>
     */
    private function grant(Order $order): Collection
    {
        // access_mail_queued_at gravado na transação da primeira concessão atua como marcador
        // de que este pedido já concedeu acesso, evitando renovação de vigência em reentregas.
        $firstGrantForOrder = $order->access_mail_queued_at === null;

        // Por pedido: a conta é uma só, independentemente de quantos cursos
        // foram comprados.
        [$user, $isNewUser] = $this->resolveUser($order);

        $order->loadMissing('items.course');

        // Por item: uma matrícula por curso (ADR-0006, RF-06).
        $enrollments = $order->items->map(
            fn (OrderItem $item): Enrollment => $this->enroll($order, $user, $item, $firstGrantForOrder)
        );

        if ($isNewUser) {
            $order->update(['granted_new_user' => true]);
            $order->granted_new_user = true;
        }

        // Por pedido: UM e-mail listando os N cursos (RF-07, CA-10). O marcador no pedido
        // impede duplicidade nas reentregas RECEIVED/CONFIRMED (CA-11).
        if ($order->access_mail_queued_at === null) {
            $courses = $order->items->pluck('course')->filter();

            Mail::to($user->email)->queue((new CourseAccessGrantedMail(
                user: $user,
                courses: $courses,
                // Link de definição de senha SÓ para conta nova. Numa conta que
                // já existe, mandar link de credencial permitiria a um terceiro
                // disparar redefinição de senha alheia só pagando um curso.
                passwordSetupUrl: $order->granted_new_user ? $this->passwordSetupUrl($user) : null,
            ))->onConnection('course-access')->beforeCommit());

            $order->update(['access_mail_queued_at' => now()]);
        }

        return new Collection($enrollments->all());
    }

    /**
     * @return array{0: User, 1: bool}
     */
    private function resolveUser(Order $order): array
    {
        $existing = User::query()->where('email', $order->buyer_email)->first();

        if ($existing instanceof User) {
            // Conta existente é reaproveitada sem tocar em nada da
            // autenticação dela: nem senha, nem is_approved, nem verificação.
            if ($order->user_id === null) {
                $order->update(['user_id' => $existing->getKey()]);
            }

            return [$existing, false];
        }

        $user = User::create([
            'name' => $order->buyer_name,
            'email' => $order->buyer_email,
            // Senha aleatória: o acesso real vem pelo link de definição.
            'password' => Str::password(32),
            // O pagamento confirmado É a aprovação. Sem isso o aluno paga e
            // fica barrado na porta do painel (gate verificado em §2.4).
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $order->update(['user_id' => $user->getKey()]);

        return [$user, true];
    }

    private function enroll(Order $order, User $user, OrderItem $item, bool $firstGrantForOrder): Enrollment
    {
        $attributes = [
            'organization_id' => $order->organization_id,
            'user_id' => $user->getKey(),
            'course_id' => $item->course_id,
        ];

        try {
            $enrollment = Enrollment::firstOrCreate($attributes, [
                'status' => EnrollmentStatus::Active,
                'purchase_type' => $order->purchase_type,
                'enrolled_at' => now(),
                'expires_at' => now()->addYear(),
            ]);
        } catch (UniqueConstraintViolationException) {
            // Entregas simultâneas de RECEIVED e CONFIRMED passam pelo
            // firstOrCreate ao mesmo tempo. O unique do banco resolve o
            // empate; aqui apenas lemos o vencedor. Não é erro.
            $enrollment = Enrollment::where($attributes)->firstOrFail();
            if ($order->purchase_type) {
                $enrollment->update(['purchase_type' => $order->purchase_type]);
            }
        }

        if ($firstGrantForOrder && ! $enrollment->wasRecentlyCreated) {
            $isExpired = $enrollment->expires_at !== null && $enrollment->expires_at <= now();
            $isNotActive = $enrollment->status !== EnrollmentStatus::Active;

            if ($isNotActive || $isExpired) {
                $enrollment->update([
                    'status' => EnrollmentStatus::Active,
                    'purchase_type' => $order->purchase_type,
                    'expires_at' => now()->addYear(),
                ]);
            } else {
                Log::warning('compra redundante: matrícula já ativa; avaliar estorno manual', [
                    'order_number' => $order->order_number,
                    'course_id' => $item->course_id,
                ]);
            }
        }

        return $enrollment;
    }

    private function passwordSetupUrl(User $user): string
    {
        $token = Password::broker()->createToken($user);

        return route('filament.auth.auth.password-reset.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);
    }
}
