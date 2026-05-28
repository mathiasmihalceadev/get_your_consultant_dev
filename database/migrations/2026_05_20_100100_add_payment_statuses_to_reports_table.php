<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('not_accessible', 'awaiting_payment', 'payment_processing', 'payment_cancelled', 'payment_failed', 'pending', 'to_be_sent', 'sent', 'error') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::table('reports')
            ->whereIn('status', ['awaiting_payment', 'payment_processing', 'payment_cancelled'])
            ->update(['status' => 'pending']);

        DB::table('reports')
            ->where('status', 'payment_failed')
            ->update(['status' => 'error']);

        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('not_accessible', 'pending', 'to_be_sent', 'sent', 'error') NOT NULL DEFAULT 'pending'");
    }
};