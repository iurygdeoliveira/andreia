<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();

            // Integridade referencial. O EAFS e mono-tenant (DEC-07), entao esta
            // coluna nao e eixo de isolamento: e sempre derivada do curso.
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Nulo ate o pagamento ser confirmado: a conta do aluno so nasce
            // depois que o dinheiro entra (DEC-01).
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();

            // ULID em vez de Str::random(8): sob constraint unique, random
            // colide eventualmente e derruba a compra (correcao C4).
            $table->string('order_number')->unique();

            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('buyer_phone');
            $table->string('buyer_document');

            // Centavos. O model expoe reais via MoneyCast (DEC-05).
            $table->integer('total_amount');

            $table->string('payment_method')->default('pix');
            $table->string('payment_status')->default('pending');

            $table->string('asaas_customer_id')->nullable();

            // Unique: a chave de idempotencia do webhook. O Asaas entrega o
            // mesmo pagamento mais de uma vez (RECEIVED e CONFIRMED).
            $table->string('payment_id')->nullable()->unique();

            $table->longText('payment_qr_code_image')->nullable();
            $table->longText('payment_qr_code_payload')->nullable();
            $table->dateTime('payment_qr_code_expiration_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->boolean('granted_new_user')->default(false);
            $table->timestamp('access_mail_queued_at')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'payment_status']);
            $table->index(['course_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
