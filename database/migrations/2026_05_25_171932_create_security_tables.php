<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_blocked_ips', function (Blueprint $table): void {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->string('reason');
            $table->string('cloudflare_rule_id')->nullable();
            $table->timestamp('blocked_at');
            $table->timestamp('unblocked_at')->nullable();
        });

        Schema::create('security_events', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('ip_address', 45);
            $table->string('email')->nullable();
            $table->string('domain')->nullable();
            $table->string('path')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('security_blocked_ips');
    }
};
