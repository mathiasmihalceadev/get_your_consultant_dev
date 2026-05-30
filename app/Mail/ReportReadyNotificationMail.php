<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportReadyNotificationMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Report $report,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->copy()['subject'] . ' #' . $this->report->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-ready-notification',
            with: [
                'report' => $this->report,
                'copy' => $this->copy(),
                'reportTypeLabel' => $this->reportTypeLabel(),
                'adminUrl' => route('admin.reports.show', ['id' => $this->report->id]),
                'processedAt' => $this->report->processed_at?->format('d.m.Y H:i'),
            ],
        );
    }

    private function reportTypeLabel(): string
    {
        $translations = $this->loadTranslations($this->report->locale ?? 'en');
        $key = 'type_' . $this->report->report_type;

        return $translations[$key] ?? $this->report->report_type;
    }

    private function copy(): array
    {
        return ($this->report->locale ?? 'en') === 'ro'
            ? [
                'subject' => 'Raport pregătit pentru trimitere',
                'title' => 'Un raport nou trebuie trimis către client.',
                'body' => 'Raportul a fost generat și a intrat în starea „De trimis”.',
                'customer' => 'Email client',
                'listing' => 'URL proprietate',
                'type' => 'Tip raport',
                'processed_at' => 'Generat la',
                'cta' => 'Deschide raportul în admin',
            ]
            : [
                'subject' => 'Report ready to send',
                'title' => 'A new report needs to be sent to the customer.',
                'body' => 'The report was generated and is now waiting in the “To be sent” state.',
                'customer' => 'Customer email',
                'listing' => 'Property URL',
                'type' => 'Report type',
                'processed_at' => 'Generated at',
                'cta' => 'Open report in admin',
            ];
    }

    private function loadTranslations(string $locale): array
    {
        $path = lang_path("{$locale}.json");

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }
}