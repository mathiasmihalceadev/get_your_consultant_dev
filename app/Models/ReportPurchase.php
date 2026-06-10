<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReportPurchase extends Model
{
    protected $fillable = [
        'report_id',
        'report_type',
        'locale',
        'email',
        'status',
        'amount_subtotal',
        'amount_total',
        'currency',
        'paid_currency',
        'base_currency',
        'base_amount_minor',
        'checkout_amount_minor',
        'exchange_rate',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'stripe_price_id',
        'stripe_product_id',
        'customer_email',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_details',
        'checkout_session_payload',
        'payment_intent_payload',
        'latest_webhook_event_id',
        'latest_webhook_event_type',
        'latest_webhook_payload',
        'metadata',
        'affiliate_tag_id',
        'affiliate_ref',
        'checkout_started_at',
        'paid_at',
        'failed_at',
        'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'customer_address' => 'array',
            'customer_details' => 'array',
            'checkout_session_payload' => 'array',
            'payment_intent_payload' => 'array',
            'latest_webhook_payload' => 'array',
            'metadata' => 'array',
            'exchange_rate' => 'decimal:6',
            'checkout_started_at' => 'datetime',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function affiliateTag(): BelongsTo
    {
        return $this->belongsTo(AffiliateTag::class);
    }

    public function smartBillInvoice(): HasOne
    {
        return $this->hasOne(SmartBillInvoice::class);
    }
}
