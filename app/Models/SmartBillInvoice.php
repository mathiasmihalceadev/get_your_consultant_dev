<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartBillInvoice extends Model
{
    protected $fillable = [
        'report_id',
        'report_purchase_id',
        'status',
        'payment_status',
        'company_vat_code',
        'invoice_series',
        'invoice_number',
        'document_id',
        'invoice_currency',
        'invoice_language',
        'payment_type',
        'document_url',
        'document_view_url',
        'file_url',
        'download_url',
        'invoice_request_payload',
        'invoice_response_payload',
        'payment_request_payload',
        'payment_response_payload',
        'error_message',
        'issued_at',
        'payment_registered_at',
        'last_attempt_at',
    ];

    protected function casts(): array
    {
        return [
            'invoice_request_payload' => 'array',
            'invoice_response_payload' => 'array',
            'payment_request_payload' => 'array',
            'payment_response_payload' => 'array',
            'issued_at' => 'datetime',
            'payment_registered_at' => 'datetime',
            'last_attempt_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(ReportPurchase::class, 'report_purchase_id');
    }
}