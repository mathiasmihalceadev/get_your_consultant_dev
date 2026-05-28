<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smart_bill_invoices', function (Blueprint $table) {
            $table->text('download_url')->nullable()->after('file_url');
        });
    }

    public function down(): void
    {
        Schema::table('smart_bill_invoices', function (Blueprint $table) {
            $table->dropColumn('download_url');
        });
    }
};