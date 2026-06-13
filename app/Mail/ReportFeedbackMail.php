<?php

namespace App\Mail;

use App\Models\Report;
use App\Support\LocalizedUrl;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportFeedbackMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Report $report,
    ) {}

    public function envelope(): Envelope
    {
        $locale = $this->reportLocale();

        return new Envelope(
            subject: $locale === 'ro'
                ? "Cum \u{021B}i s-a p\u{0103}rut raportul GetYourConsultant?"
                : 'How was your GetYourConsultant report?',
        );
    }

    public function content(): Content
    {
        $locale = $this->reportLocale();

        return new Content(
            view: $this->templateView(),
            with: [
                'report' => $this->report,
                'locale' => $locale,
                'copy' => $this->copy($locale),
                'feedbackUrl' => LocalizedUrl::publicUrlForLocale($locale, '/feedback/'.$this->report->page_token),
                'logoUrl' => LocalizedUrl::publicUrlForLocale($locale, '/images/main-logo-transparent.png'),
                'websiteUrl' => LocalizedUrl::publicUrlForLocale($locale, '/'),
                'currentYear' => now()->year,
            ],
        );
    }

    private function reportLocale(): string
    {
        return strtolower((string) ($this->report->locale ?? 'ro')) === 'en' ? 'en' : 'ro';
    }

    private function templateView(): string
    {
        return match ((string) config('services.report_feedback_email.template', 'modern')) {
            'table' => 'emails.report-feedback-table',
            default => 'emails.report-feedback',
        };
    }

    private function copy(string $locale): array
    {
        if ($locale === 'en') {
            return [
                'title' => 'How was your GetYourConsultant report?',
                'thanks' => 'Thank you for using GetYourConsultant™.',
                'intro' => 'We are constantly improving the platform, and your feedback helps us a lot.',
                'prompt' => 'It would help us if you could quickly answer a few questions:',
                'questions' => [
                    ['label' => 'What rating would you give the report?', 'hint' => '1-10'],
                    ['label' => 'Which information did you find most useful?', 'hint' => null],
                    ['label' => 'What would you like to see added to the report?', 'hint' => null],
                    ['label' => 'Would you recommend the platform to a friend?', 'hint' => 'YES / NO'],
                    ['label' => 'What would make you trust this report even more?', 'hint' => null],
                ],
                'cta' => 'Complete feedback',
                'websiteLabel' => 'getyourconsultant.com',
            ];
        }

        return [
            'title' => "Cum \u{021B}i s-a p\u{0103}rut raportul GetYourConsultant?",
            'thanks' => "Mul\u{021B}umim c\u{0103} ai folosit GetYourConsultant™.",
            'intro' => "\u{00CE}ncerc\u{0103}m s\u{0103} \u{00EE}mbun\u{0103}t\u{0103}\u{021B}im constant platforma \u{0219}i feedback-ul t\u{0103}u ne ajut\u{0103} enorm.",
            'prompt' => "Ne-ar ajuta dac\u{0103} ne r\u{0103}spunzi rapid la c\u{00E2}teva \u{00EE}ntreb\u{0103}ri:",
            'questions' => [
                ['label' => "Ce not\u{0103} ai acorda raportului?", 'hint' => '1-10'],
                ['label' => "Care informa\u{021B}ie \u{021B}i s-a p\u{0103}rut cea mai util\u{0103}?", 'hint' => null],
                ['label' => "Ce informa\u{021B}ii ai fi vrut s\u{0103} g\u{0103}se\u{0219}ti \u{00EE}n acest raport \u{0219}i nu au fost incluse?", 'hint' => null],
                ['label' => 'Ai recomanda platforma unui prieten?', 'hint' => 'DA / NU'],
                ['label' => "Ce te-ar face s\u{0103} ai \u{0219}i mai mult\u{0103} \u{00EE}ncredere \u{00EE}n acest raport?", 'hint' => null],
            ],
            'cta' => "Completeaz\u{0103} feedback-ul",
            'websiteLabel' => 'getyourconsultant.ro',
        ];
    }
}
