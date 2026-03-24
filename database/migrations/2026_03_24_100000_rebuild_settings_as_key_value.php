<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Read existing settings before dropping
        $old = DB::table('settings')->first();

        Schema::dropIfExists('settings');

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // Migrate existing data if present
        if ($old) {
            $mapping = [
                'rental_living_prompt' => $old->rental_living_prompt ?? null,
                'rental_living_prompt_ro' => $old->rental_living_prompt_ro ?? null,
                'rental_business_prompt' => $old->rental_business_prompt ?? null,
                'rental_business_prompt_ro' => $old->rental_business_prompt_ro ?? null,
                'buying_living_prompt' => $old->buying_living_prompt ?? null,
                'buying_living_prompt_ro' => $old->buying_living_prompt_ro ?? null,
                'buying_business_prompt' => $old->buying_business_prompt ?? null,
                'buying_business_prompt_ro' => $old->buying_business_prompt_ro ?? null,
                'auto_send' => isset($old->auto_send) ? ($old->auto_send ? '1' : '0') : '0',
            ];

            $now = now();
            foreach ($mapping as $key => $value) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->longText('rental_living_prompt')->nullable();
            $table->longText('rental_living_prompt_ro')->nullable();
            $table->longText('rental_business_prompt')->nullable();
            $table->longText('rental_business_prompt_ro')->nullable();
            $table->longText('buying_living_prompt')->nullable();
            $table->longText('buying_living_prompt_ro')->nullable();
            $table->longText('buying_business_prompt')->nullable();
            $table->longText('buying_business_prompt_ro')->nullable();
            $table->boolean('auto_send')->default(false);
            $table->timestamps();
        });
    }
};
