<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Reconcilia e revoga o acesso aos cursos após confirmação de estorno integral (SEC-03, RF-09).
 *
 * Transacional e idempotente por obrigação:
 * - O Asaas entrega notificações repetidas do webhook.
 * - Preserva acessos respaldados por outros pedidos pagos ou concessões independentes.
 * - Estorno parcial é tratado separadamente e não revoga a matrícula.
 */
class RevokeCourseAccessAction
{
    /**
     * @return Collection<int, Enrollment>
     */
    public function execute(Order $order): Collection
    {
        return DB::transaction(function () use ($order): Collection {
            $order = Order::query()->lockForUpdate()->findOrFail($order->getKey());

            // Apenas estorno integral revoga o acesso
            if ($order->payment_status !== OrderStatus::Refunded) {
                return new Collection;
            }

            $user = $order->user;
            if (! $user instanceof User) {
                $user = User::query()->where('email', $order->buyer_email)->first();
            }

            if (! $user instanceof User) {
                Log::warning('Revogação de acesso ignorada: usuário não localizado para o pedido estornado.', [
                    'order_number' => $order->order_number,
                    'buyer_email' => $order->buyer_email,
                ]);

                return new Collection;
            }

            $order->loadMissing('items');

            $revokedEnrollments = new Collection;

            foreach ($order->items as $item) {
                $courseId = $item->course_id;

                // Preservar acesso caso o usuário tenha outro pedido pago e válido para o mesmo curso (RF-09)
                $hasOtherPaidOrder = Order::query()
                    ->where('organization_id', $order->organization_id)
                    ->where(function ($q) use ($user, $order): void {
                        $q->where('user_id', $user->getKey())
                            ->orWhere('buyer_email', $order->buyer_email);
                    })
                    ->whereKeyNot($order->getKey())
                    ->whereIn('payment_status', OrderStatus::accessGranting())
                    ->whereHas('items', fn ($q) => $q->where('course_id', $courseId))
                    ->exists();

                if ($hasOtherPaidOrder) {
                    Log::info('Acesso ao curso preservado: usuário possui outro pedido pago válido para o curso.', [
                        'user_id' => $user->getKey(),
                        'course_id' => $courseId,
                        'refunded_order' => $order->order_number,
                    ]);

                    continue;
                }

                // Localiza a matrícula ativa correspondente
                $enrollment = Enrollment::query()
                    ->lockForUpdate()
                    ->where('organization_id', $order->organization_id)
                    ->where('user_id', $user->getKey())
                    ->where('course_id', $courseId)
                    ->first();

                if (! $enrollment instanceof Enrollment) {
                    continue;
                }

                if ($enrollment->status !== EnrollmentStatus::Cancelled) {
                    $enrollment->update([
                        'status' => EnrollmentStatus::Cancelled,
                    ]);

                    Log::info('Acesso ao curso revogado com sucesso após estorno integral.', [
                        'enrollment_id' => $enrollment->getKey(),
                        'user_id' => $user->getKey(),
                        'course_id' => $courseId,
                        'order_number' => $order->order_number,
                    ]);

                    $revokedEnrollments->push($enrollment);
                }
            }

            return $revokedEnrollments;
        });
    }
}
