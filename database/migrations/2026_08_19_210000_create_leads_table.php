<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('profile')->nullable();
            $table->timestamps();

            // O funil de compra ainda nao existe (ver UC-04 pendente). Ate la o
            // lead e trabalhado manualmente, e a consulta natural e "quem se
            // inscreveu neste curso, do mais recente para o mais antigo".
            $table->index(['course_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
