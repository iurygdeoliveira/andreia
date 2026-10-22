<?php

declare(strict_types=1);

use App\Actions\RevokeCourseAccessAction;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('T07 — Preservar acesso por pedido parcialmente estornado', function () {
    it('preserva matricula ativa quando usuario possui outro pedido PartiallyRefunded do mesmo curso', function () {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['email' => 'aluno@eafs.com.br']);
        $course = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
        ]);

        // Matrícula ativa
        $enrollment = Enrollment::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Pedido A (estornado integralmente)
        $orderA = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'buyer_email' => $user->email,
            'payment_status' => OrderStatus::Refunded,
            'order_number' => 'ORD-A-001',
        ]);

        // Pedido B (parcialmente estornado - ainda concede acesso)
        $orderB = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'buyer_email' => $user->email,
            'payment_status' => OrderStatus::PartiallyRefunded,
            'order_number' => 'ORD-B-002',
        ]);

        $action = app(RevokeCourseAccessAction::class);
        $revoked = $action->execute($orderA);

        expect($revoked)->toBeEmpty();
        expect($enrollment->fresh()->status)->toBe(EnrollmentStatus::Active);
    });

    it('revoga matricula quando nao existe outro pedido que conceda acesso', function () {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['email' => 'aluno@eafs.com.br']);
        $course = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
        ]);

        $enrollment = Enrollment::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        $orderA = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'buyer_email' => $user->email,
            'payment_status' => OrderStatus::Refunded,
            'order_number' => 'ORD-A-002',
        ]);

        $action = app(RevokeCourseAccessAction::class);
        $revoked = $action->execute($orderA);

        expect($revoked)->toHaveCount(1);
        expect($enrollment->fresh()->status)->toBe(EnrollmentStatus::Cancelled);
    });

    it('nao preserva acesso se o outro pedido PartiallyRefunded pertencer a outro usuario', function () {
        $org = Organization::factory()->create();
        $userA = User::factory()->create(['email' => 'alunoA@eafs.com.br']);
        $userB = User::factory()->create(['email' => 'alunoB@eafs.com.br']);
        $course = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
        ]);

        $enrollmentA = Enrollment::create([
            'organization_id' => $org->id,
            'user_id' => $userA->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        $orderA = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'user_id' => $userA->id,
            'buyer_email' => $userA->email,
            'payment_status' => OrderStatus::Refunded,
            'order_number' => 'ORD-A-003',
        ]);

        // Pedido de userB, não userA
        $orderB = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'user_id' => $userB->id,
            'buyer_email' => $userB->email,
            'payment_status' => OrderStatus::PartiallyRefunded,
            'order_number' => 'ORD-B-003',
        ]);

        $action = app(RevokeCourseAccessAction::class);
        $revoked = $action->execute($orderA);

        expect($revoked)->toHaveCount(1);
        expect($enrollmentA->fresh()->status)->toBe(EnrollmentStatus::Cancelled);
    });

    it('nao preserva acesso se o outro pedido PartiallyRefunded for de outro curso', function () {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['email' => 'aluno@eafs.com.br']);
        $course1 = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
        ]);
        $course2 = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
        ]);

        $enrollment = Enrollment::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'course_id' => $course1->id,
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        $orderA = Order::factory()->withCourses($course1)->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'buyer_email' => $user->email,
            'payment_status' => OrderStatus::Refunded,
            'order_number' => 'ORD-A-004',
        ]);

        // Pedido do course2, não course1
        $orderB = Order::factory()->withCourses($course2)->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'buyer_email' => $user->email,
            'payment_status' => OrderStatus::PartiallyRefunded,
            'order_number' => 'ORD-B-004',
        ]);

        $action = app(RevokeCourseAccessAction::class);
        $revoked = $action->execute($orderA);

        expect($revoked)->toHaveCount(1);
        expect($enrollment->fresh()->status)->toBe(EnrollmentStatus::Cancelled);
    });
});
