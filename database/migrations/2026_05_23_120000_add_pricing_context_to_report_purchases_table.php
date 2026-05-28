<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_purchases', function (Blueprint $table) {
            $table->string('paid_currency', 3)->nullable()->after('currency');
            $table->string('base_currency', 3)->default('eur')->after('paid_currency');
            $table->unsignedBigInteger('base_amount_minor')->nullable()->after('base_currency');
            $table->unsignedBigInteger('checkout_amount_minor')->nullable()->after('base_amount_minor');
            $table->decimal('exchange_rate', 12, 6)->nullable()->after('checkout_amount_minor');
            $table->string('stripe_product_id')->nullable()->after('stripe_price_id');
        });
    }

    public function down(): void
    {
        Schema::table('report_purchases', function (Blueprint $table) {
            $table->dropColumn([
                'paid_currency',
                'base_currency',
                'base_amount_minor',
                'checkout_amount_minor',
                'exchange_rate',
                'stripe_product_id',
            ]);
        });
    }
};