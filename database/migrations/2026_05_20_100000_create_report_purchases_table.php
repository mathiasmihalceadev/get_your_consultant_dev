<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('report_type');
            $table->string('locale', 5)->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('checkout_created');
            $table->unsignedBigInteger('amount_subtotal')->nullable();
            $table->unsignedBigInteger('amount_total')->nullable();
            $table->string('currency', 3)->default('eur');
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('stripe_price_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->json('customer_address')->nullable();
            $table->json('customer_details')->nullable();
            $table->json('checkout_session_payload')->nullable();
            $table->json('payment_intent_payload')->nullable();
            $table->string('latest_webhook_event_id')->nullable();
            $table->string('latest_webhook_event_type')->nullable();
            $table->json('latest_webhook_payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('checkout_started_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_purchases');
    }
};