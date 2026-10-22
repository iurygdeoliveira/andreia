<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->integer('price_in_cents');
            $table->uuid('checkout_code')->unique();
            $table->string('status')->default('draft');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['organization_id', 'status'], 'idx_courses_org_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
