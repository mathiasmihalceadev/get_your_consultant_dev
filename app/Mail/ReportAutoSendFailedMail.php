<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportAutoSendFailedMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Report $report,
        public string $errorMessage,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Automatic report email failed #{$this->report->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-auto-send-failed',
            with: [
                'report' => $this->report,
                'errorMessage' => $this->errorMessage,
                'adminUrl' => route('admin.reports.show', ['id' => $this->report->id]),
            ],
        );
    }
}
