<?php

namespace App\Mail;

use App\Models\Report;
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
        $typeLabel = match ($this->report->report_type) {
            'purchase' => 'Purchase',
            'rental' => 'Rental',
            'commercial' => 'Commercial',
        };

        return new Envelope(
            subject: "Your {$typeLabel} Property Report is Ready",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report',
            with: [
                'report' => $this->report,
                'typeLabel' => match ($this->report->report_type) {
                    'purchase' => 'Purchase',
                    'rental' => 'Rental',
                    'commercial' => 'Commercial',
                },
                'statusUrl' => url("/report/{$this->report->page_token}"),
            ],
        );
    }

    public function attachments(): array
    {
        $path = storage_path("app/reports/{$this->report->page_token}.pdf");

        if (!file_exists($path)) {
            return [];
        }

        return [
            Attachment::fromPath($path)
                ->as('property-report.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
