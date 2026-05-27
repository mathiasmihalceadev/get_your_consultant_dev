<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->boolean('is_test')->default(false)->after('locale');
        });

        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('not_accessible', 'awaiting_payment', 'payment_processing', 'payment_cancelled', 'payment_failed', 'test_completed', 'pending', 'to_be_sent', 'sent', 'error') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('not_accessible', 'awaiting_payment', 'payment_processing', 'payment_cancelled', 'payment_failed', 'pending', 'to_be_sent', 'sent', 'error') NOT NULL DEFAULT 'pending'");

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('is_test');
        });
    }
};