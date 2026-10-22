<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('purchase_type')->default('premium')->after('total_amount');
            $table->index('purchase_type');
        });

        Schema::table('enrollments', function (Blueprint $table): void {
            $table->string('purchase_type')->default('premium')->after('status');
            $table->index('purchase_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['purchase_type']);
            $table->dropColumn('purchase_type');
        });

        Schema::table('enrollments', function (Blueprint $table): void {
            $table->dropIndex(['purchase_type']);
            $table->dropColumn('purchase_type');
        });
    }
};
