<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    protected $fillable = [
        'report_type',
        'url',
        'email',
        'locale',
        'is_test',
        'status',
        'report_url',
        'page_token',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_test' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(ReportPurchase::class);
    }

    public function latestPurchase(): HasOne
    {
        return $this->hasOne(ReportPurchase::class)->latestOfMany();
    }

    public function smartBillInvoices(): HasMany
    {
        return $this->hasMany(SmartBillInvoice::class);
    }

    public function pdfStorageFilename(): string
    {
        return sprintf('gyc_%05d.pdf', $this->id);
    }

    public function resolvedPdfStorageFilename(): string
    {
        $reportUrl = (string) ($this->report_url ?? '');

        if ($reportUrl !== '') {
            $path = parse_url($reportUrl, PHP_URL_PATH) ?: $reportUrl;
            $basename = basename($path);

            if ($basename !== '' && $basename !== '.' && str_ends_with(strtolower($basename), '.pdf')) {
                return $basename;
            }
        }

        return $this->pdfStorageFilename();
    }

    public function pdfStoragePath(): string
    {
        return storage_path('app/public/reports/' . $this->resolvedPdfStorageFilename());
    }

    public function pdfPublicUrl(): string
    {
        return '/storage/reports/' . $this->pdfStorageFilename();
    }
}
