<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('rental_living_prompt_ro')->nullable()->after('rental_living_prompt');
            $table->longText('rental_business_prompt_ro')->nullable()->after('rental_business_prompt');
            $table->longText('buying_living_prompt_ro')->nullable()->after('buying_living_prompt');
            $table->longText('buying_business_prompt_ro')->nullable()->after('buying_business_prompt');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'rental_living_prompt_ro',
                'rental_business_prompt_ro',
                'buying_living_prompt_ro',
                'buying_business_prompt_ro',
            ]);
        });
    }
};
