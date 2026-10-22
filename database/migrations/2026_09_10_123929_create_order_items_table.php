<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Centavos. O model expoe reais via MoneyCast, igual a
            // Course::price_in_cents e Order::total_amount. O preco e
            // congelado aqui: alterar o catalogo depois nao reescreve
            // o historico de quem ja comprou.
            $table->integer('unit_price');

            $table->timestamps();

            // Sem coluna quantity de proposito (RN-01): curso e matricula,
            // nao ingresso. O unique abaixo e a regra, nao um contador.
            $table->unique(['order_id', 'course_id']);

            // Consulta reversa: quem comprou este curso.
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
