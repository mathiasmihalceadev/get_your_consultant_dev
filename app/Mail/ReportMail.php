<?php

namespace App\Mail;

use App\Models\Report;
use App\Support\LocalizedUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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

        return new Content(
            view: 'emails.report',
            with: [
                'report' => $this->report,
                'typeLabel' => $typeLabel,
                'statusUrl' => LocalizedUrl::urlForLocale($locale, "/report/{$this->report->page_token}"),
                'trans' => $translations,
            ],
        );
    }

    public function attachments(): array
    {
        $path = $this->report->pdfStoragePath();

        if (!file_exists($path)) {
            return [];
        }

        return [
            Attachment::fromPath($path)
                ->as('raport.pdf')
                ->withMime('application/pdf'),
        ];
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
