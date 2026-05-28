<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_bill_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('report_purchase_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('company_vat_code')->nullable();
            $table->string('invoice_series')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('document_id')->nullable();
            $table->string('invoice_currency', 3)->nullable();
            $table->string('invoice_language', 5)->nullable();
            $table->string('payment_type')->nullable();
            $table->text('document_url')->nullable();
            $table->text('document_view_url')->nullable();
            $table->text('file_url')->nullable();
            $table->json('invoice_request_payload')->nullable();
            $table->json('invoice_response_payload')->nullable();
            $table->json('payment_request_payload')->nullable();
            $table->json('payment_response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('payment_registered_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_bill_invoices');
    }
};