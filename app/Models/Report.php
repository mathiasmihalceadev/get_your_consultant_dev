<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    private const PDF_STORAGE_NUMBER_OFFSET = 1999;
    private const PDF_STORAGE_DIRECTORY = 'reports';

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
        'feedback_sent_at',
        'affiliate_tag_id',
        'affiliate_ref',
    ];

    protected function casts(): array
    {
        return [
            'is_test' => 'boolean',
            'processed_at' => 'datetime',
            'feedback_sent_at' => 'datetime',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(ReportPurchase::class);
    }

    public function affiliateTag(): BelongsTo
    {
        return $this->belongsTo(AffiliateTag::class);
    }

    public function latestPurchase(): HasOne
    {
        return $this->hasOne(ReportPurchase::class)->latestOfMany();
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(ReportFeedback::class);
    }

    public function smartBillInvoices(): HasMany
    {
        return $this->hasMany(SmartBillInvoice::class);
    }

    public function pdfStorageFilename(): string
    {
        return sprintf('gyc_%05d.pdf', $this->pdfStorageNumber());
    }

    public function pdfStorageRelativePath(): string
    {
        return self::PDF_STORAGE_DIRECTORY . '/' . $this->resolvedPdfStorageFilename();
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
        return storage_path('app/private/' . $this->pdfStorageRelativePath());
    }

    public function legacyPublicPdfStoragePath(): string
    {
        return storage_path('app/public/' . self::PDF_STORAGE_DIRECTORY . '/' . $this->resolvedPdfStorageFilename());
    }

    public function storedPdfPath(): ?string
    {
        $privatePath = $this->pdfStoragePath();

        if (is_file($privatePath)) {
            return $privatePath;
        }

        $legacyPath = $this->legacyPublicPdfStoragePath();

        return is_file($legacyPath) ? $legacyPath : null;
    }

    public function hasStoredPdf(): bool
    {
        return $this->storedPdfPath() !== null;
    }

    public function pdfStorageNumber(): int
    {
        return (int) $this->id + self::PDF_STORAGE_NUMBER_OFFSET;
    }
}
