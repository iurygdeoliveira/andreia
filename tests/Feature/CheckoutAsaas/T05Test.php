<?php

declare(strict_types=1);

use App\Actions\GrantCourseAccessAction;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Mail\CourseAccessGrantedMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

describe('T05 — Reativação idempotente por um ano', function () {
    beforeEach(function () {
        Mail::fake();

        $this->org = Organization::factory()->create();
        $this->course = Course::factory()->create([
            'organization_id' => $this->org->id,
            'status' => CourseStatus::Published,
            'price_in_cents' => 100.0,
        ]);
        $this->user = User::factory()->create([
            'email' => 'aluno_reativacao@teste.com',
        ]);
    });

    it('reativa matrícula Cancelled para Active com vigência de um ano em novo pedido', function () {
        $enrollment = Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'status' => EnrollmentStatus::Cancelled,
            'expires_at' => now()->subDays(10),
        ]);

        $order = Order::factory()->withCourses($this->course)->create([
            'organization_id' => $this->org->id,
            'buyer_email' => $this->user->email,
            'payment_status' => OrderStatus::Received,
            'paid_at' => now(),
            'access_mail_queued_at' => null,
        ]);

        $action = app(GrantCourseAccessAction::class);
        $action->execute($order);

        $enrollment->refresh();
        expect($enrollment->status)->toBe(EnrollmentStatus::Active)
            ->and($enrollment->expires_at->isFuture())->toBeTrue()
            ->and($enrollment->expires_at->diffInDays(now()->addYear()))->toBeLessThanOrEqual(1);

        Mail::assertQueued(CourseAccessGrantedMail::class, 1);
    });

    it('reativa matrícula expirada (ou expires_at <= now) para Active com vigência de um ano', function () {
        $enrollment = Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => now()->subDay(),
        ]);

        $order = Order::factory()->withCourses($this->course)->create([
            'organization_id' => $this->org->id,
            'buyer_email' => $this->user->email,
            'payment_status' => OrderStatus::Received,
            'paid_at' => now(),
            'access_mail_queued_at' => null,
        ]);

        $action = app(GrantCourseAccessAction::class);
        $action->execute($order);

        $enrollment->refresh();
        expect($enrollment->status)->toBe(EnrollmentStatus::Active)
            ->and($enrollment->expires_at->diffInDays(now()->addYear()))->toBeLessThanOrEqual(1);
    });

    it('matrícula ativa e vigente mantém prazo e gera log de compra redundante sem estender nem estornar', function () {
        $originalExpiry = now()->addMonths(6);
        $enrollment = Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => $originalExpiry,
        ]);

        $order = Order::factory()->withCourses($this->course)->create([
            'organization_id' => $this->org->id,
            'buyer_email' => $this->user->email,
            'payment_status' => OrderStatus::Received,
            'paid_at' => now(),
            'access_mail_queued_at' => null,
        ]);

        Log::spy();

        $action = app(GrantCourseAccessAction::class);
        $action->execute($order);

        $enrollment->refresh();
        // Prazo não foi estendido para 1 ano
        expect($enrollment->expires_at->timestamp)->toBe($originalExpiry->timestamp);

        Log::shouldHaveReceived('warning')
            ->withArgs(function ($message, $context) use ($order) {
                return str_contains($message, 'compra redundante')
                    && $context['order_number'] === $order->order_number;
            });
    });

    it('reentrega do mesmo pedido não renova prazo nem reenvia e-mail', function () {
        $order = Order::factory()->withCourses($this->course)->create([
            'organization_id' => $this->org->id,
            'buyer_email' => $this->user->email,
            'payment_status' => OrderStatus::Received,
            'paid_at' => now(),
            'access_mail_queued_at' => null,
        ]);

        $action = app(GrantCourseAccessAction::class);
        $action->execute($order);

        $enrollment = Enrollment::where('user_id', $this->user->id)
            ->where('course_id', $this->course->id)
            ->first();

        $firstExpiry = $enrollment->expires_at;

        // Avança 1 mês no tempo e reentrega o mesmo pedido
        $this->travel(1)->month();

        $order->refresh();
        $action->execute($order);

        $enrollment->refresh();
        expect($enrollment->expires_at->timestamp)->toBe($firstExpiry->timestamp);

        // Apenas 1 e-mail enviado no total
        Mail::assertQueued(CourseAccessGrantedMail::class, 1);
    });
});
