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
        Schema::table('orders', function (Blueprint $table): void {
            // O índice composto morre com a coluna; removido antes da FK
            // para o PostgreSQL não reclamar de dependência (ADR-0003).
            $table->dropIndex(['course_id', 'created_at']);
            $table->dropConstrainedForeignId('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->index(['course_id', 'created_at']);
        });
    }
};
