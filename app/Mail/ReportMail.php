<?php

namespace App\Mail;

use App\Models\Report;
use App\Support\LocalizedUrl;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Report $report,
    ) {}

    public function envelope(): Envelope
    {
        $locale = $this->report->locale ?? 'en';
        $translations = $this->loadTranslations($locale);

        $subjectKey = "email_subject_{$this->report->report_type}";
        $subject = $translations[$subjectKey] ?? "Your Property Report is Ready";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $locale = $this->report->locale ?? 'en';
        $translations = $this->loadTranslations($locale);

        $typeKey = "type_{$this->report->report_type}";
        $typeLabel = $translations[$typeKey] ?? $this->report->report_type;
        $statusUrl = LocalizedUrl::urlForLocale($locale, "/report/{$this->report->page_token}");
        $downloadPath = $this->report->report_url ?: $this->report->pdfPublicUrl();

        return new Content(
            view: 'emails.report',
            with: [
                'report' => $this->report,
                'typeLabel' => $typeLabel,
                'downloadUrl' => LocalizedUrl::urlForLocale($locale, $downloadPath),
                'statusUrl' => $statusUrl,
                'contactUrl' => LocalizedUrl::urlForLocale($locale, '/contact'),
                'logoUrl' => LocalizedUrl::urlForLocale($locale, '/images/main-logo-transparent.png'),
                'contactEmail' => $locale === 'ro'
                    ? 'contact@getyourconsultant.ro'
                    : 'contact@getyourconsultant.com',
                'websiteUrl' => LocalizedUrl::urlForLocale($locale, '/'),
                'websiteLabel' => $locale === 'ro'
                    ? 'getyourconsultant.ro'
                    : 'getyourconsultant.com',
                'currentYear' => now()->year,
                'trans' => $translations,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function loadTranslations(string $locale): array
    {
        $path = lang_path("{$locale}.json");
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true) ?? [];
        }
        return [];
    }
}
