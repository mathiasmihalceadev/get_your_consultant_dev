<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update report_type enum in reports table
        DB::statement("ALTER TABLE reports MODIFY COLUMN report_type ENUM('purchase', 'rental', 'commercial', 'rental_living', 'rental_business', 'buying_living', 'buying_business') NOT NULL");

        // Map old types to new types
        DB::table('reports')->where('report_type', 'purchase')->update(['report_type' => 'buying_living']);
        DB::table('reports')->where('report_type', 'rental')->update(['report_type' => 'rental_living']);
        DB::table('reports')->where('report_type', 'commercial')->update(['report_type' => 'buying_business']);

        // Remove old enum values
        DB::statement("ALTER TABLE reports MODIFY COLUMN report_type ENUM('rental_living', 'rental_business', 'buying_living', 'buying_business') NOT NULL");

        // Update settings table: rename old columns and add new ones
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('purchase_prompt', 'buying_living_prompt');
            $table->renameColumn('rental_prompt', 'rental_living_prompt');
            $table->renameColumn('commercial_prompt', 'buying_business_prompt');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->longText('rental_business_prompt')->nullable()->after('rental_living_prompt');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('rental_business_prompt');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('buying_living_prompt', 'purchase_prompt');
            $table->renameColumn('rental_living_prompt', 'rental_prompt');
            $table->renameColumn('buying_business_prompt', 'commercial_prompt');
        });

        DB::statement("ALTER TABLE reports MODIFY COLUMN report_type ENUM('rental_living', 'rental_business', 'buying_living', 'buying_business', 'purchase', 'rental', 'commercial') NOT NULL");

        DB::table('reports')->where('report_type', 'buying_living')->update(['report_type' => 'purchase']);
        DB::table('reports')->where('report_type', 'rental_living')->update(['report_type' => 'rental']);
        DB::table('reports')->where('report_type', 'buying_business')->update(['report_type' => 'commercial']);

        DB::statement("ALTER TABLE reports MODIFY COLUMN report_type ENUM('purchase', 'rental', 'commercial') NOT NULL");
    }
};
