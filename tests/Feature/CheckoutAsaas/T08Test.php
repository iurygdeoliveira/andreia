<?php

declare(strict_types=1);

use App\Actions\GrantCourseAccessAction;
use App\Enums\CourseStatus;
use App\Enums\OrderStatus;
use App\Mail\CourseAccessGrantedMail;
use App\Models\Course;
use App\Models\Order;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

describe('T08 — Configurar as duas conexões sync', function () {
    it('possui a conexão default e a conexão course-access configuradas como sync', function () {
        expect(config('queue.default'))->toBe('sync');
        expect(config('queue.connections.course-access.driver'))->toBe('sync');
    });

    it('envia e-mail de acesso de forma síncrona sem deixar jobs na tabela jobs', function () {
        Mail::fake();

        $org = Organization::factory()->create();
        $course = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
            'title' => 'Curso de Teste Sync',
        ]);

        $order = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'buyer_name' => 'Aluno Sync',
            'buyer_email' => 'sync-aluno@eafs.com.br',
            'payment_status' => OrderStatus::Received,
            'order_number' => 'ORD-SYNC-001',
        ]);

        $action = app(GrantCourseAccessAction::class);
        $action->execute($order);

        // Verifica que o e-mail foi despachado
        Mail::assertQueued(CourseAccessGrantedMail::class, function ($mail) {
            return $mail->hasTo('sync-aluno@eafs.com.br');
        });

        // Com conexão sync, a tabela jobs no banco de dados deve estar vazia
        if (Schema::hasTable('jobs')) {
            expect(DB::table('jobs')->count())->toBe(0);
        }
    });
});
