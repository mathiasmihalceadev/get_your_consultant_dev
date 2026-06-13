@php
    $locale = strtolower((string) ($report->locale ?? 'en'));
    $isRomanian = $locale === 'ro';
    $copy = [
        'autoMessage' => $isRomanian
            ? 'Mesaj automat — te rugăm să nu răspunzi direct la acest email.'
            : 'Automated message — please do not reply directly to this email.',
        'intro' => $isRomanian
            ? 'Analiza proprietății tale a fost finalizată.'
            : 'Your property analysis has been completed.',
        'downloadTitle' => $isRomanian ? 'Fișiere atașate' : 'Attached files',
        'downloadBody' => $isRomanian
            ? ($invoiceAttached
                ? 'Raportul și factura sunt atașate acestui email.'
                : 'Raportul este atașat acestui email. Factura va fi atașată separat când este disponibilă.')
            : ($invoiceAttached
                ? 'The report and invoice are attached to this email.'
                : 'The report is attached to this email. The invoice will be attached separately when it becomes available.'),
        'supportTitle' => $isRomanian ? 'Întrebări sau nelămuriri?' : 'Questions or anything unclear?',
        'supportBody' => $isRomanian
            ? 'Răspundem rapid la orice întrebare.'
            : 'We reply quickly to any question.',
        'supportCta' => $isRomanian ? 'Contactează-ne' : 'Contact us',
        'footerBrand' => 'GetYourConsultant™',
        'pdfLabel' => 'pdf',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $trans['email_title'] ?? 'Your Property Report is Ready' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        body,
        table,
        td,
        div,
        p,
        a,
        h1,
        span {
            font-family: 'Inter', Arial, Helvetica, sans-serif !important;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f6f7fb;
            color: #2a2758;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .shell {
            max-width: 740px;
            margin: 0 auto;
            padding: 24px 16px 32px;
        }

        .topbar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .topbar td {
            vertical-align: middle;
        }

        .logo {
            display: block;
            height: 56px;
            width: auto;
        }

        .auto-message {
            font-size: 13px;
            color: #6f7595;
            text-align: right;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e8f2;
            border-radius: 28px;
            padding: 36px 44px 34px;
            box-shadow: 0 20px 44px rgba(52, 48, 106, 0.06);
        }

        .success-mark {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #e6f7eb;
            color: #34a853;
            font-size: 34px;
            line-height: 56px;
            text-align: center;
        }

        h1 {
            margin: 18px 0 12px;
            font-size: 32px;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: #20285f;
        }

        .lead {
            margin: 0;
            font-size: 15px;
            line-height: 1.75;
            color: rgba(32, 40, 95, 0.82);
        }

        .download-section {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #e9ebf4;
        }

        .download-title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #20285f;
        }

        .download-copy {
            margin: 6px 0 18px;
            font-size: 14px;
            color: rgba(32, 40, 95, 0.72);
        }

        .download-row {
            width: auto;
            border-collapse: collapse;
        }

        .download-row td {
            vertical-align: middle;
        }

        .pdf-badge-cell {
            padding-right: 12px;
        }

        .pdf-badge {
            display: inline-block;
            min-width: 36px;
            height: 42px;
            line-height: 42px;
            border-radius: 10px;
            border: 1px solid #d9deed;
            background: #ffffff;
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            color: #e05252;
            box-shadow: 0 10px 22px rgba(52, 48, 106, 0.08);
        }

        .button {
            display: inline-block;
            padding: 15px 28px;
            border-radius: 10px;
            background: #34306a;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 18px 36px rgba(52, 48, 106, 0.22);
        }

        .support-box {
            width: 100%;
            margin-top: 28px;
            border-radius: 0;
            background: #eef3ff;
            border-collapse: separate;
            border-spacing: 0;
        }

        .support-box td {
            padding: 18px 20px;
            vertical-align: middle;
        }

        .support-icon-cell {
            width: 62px;
        }

        .support-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid #4052cf;
            text-align: center;
            box-sizing: border-box;
        }

        .support-icon-mark {
            display: inline-block;
            line-height: 38px;
            font-size: 22px;
            font-weight: 700;
            color: #4052cf;
        }

        .support-title {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #20285f;
        }

        .support-copy {
            margin: 4px 0 0;
            font-size: 14px;
            color: rgba(32, 40, 95, 0.72);
        }

        .support-link {
            font-size: 14px;
            font-weight: 600;
            color: #4052cf !important;
            text-decoration: underline;
        }

        .footer {
            padding: 22px 4px 0;
            text-align: center;
        }

        .footer-brand {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #20285f;
        }

        .footer-desc {
            max-width: 540px;
            margin: 8px auto 0;
            font-size: 13px;
            color: rgba(32, 40, 95, 0.66);
        }

        .footer-contact {
            width: 100%;
            margin-top: 16px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .footer-contact td {
            padding: 8px 10px;
            font-size: 13px;
            color: rgba(32, 40, 95, 0.72);
        }

        .footer-contact a {
            color: rgba(32, 40, 95, 0.72) !important;
            text-decoration: none;
        }

        .footer-legal {
            margin: 10px 0 0;
            font-size: 12px;
            color: rgba(32, 40, 95, 0.56);
        }

        .footer-copy {
            margin: 6px 0 0;
            font-size: 12px;
            color: rgba(32, 40, 95, 0.56);
        }

        @media only screen and (max-width: 640px) {
            .topbar,
            .support-box,
            .footer-contact,
            .download-row {
                display: block !important;
                width: 100% !important;
            }

            .topbar td,
            .support-box td,
            .footer-contact td,
            .download-row td {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
            }

            .card {
                padding: 28px 20px;
            }

            h1 {
                font-size: 28px;
            }

            .pdf-badge-cell {
                padding-right: 0;
                padding-bottom: 12px;
            }

            .support-action {
                padding-top: 0 !important;
            }

            .auto-message {
                text-align: left;
                padding-top: 10px;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background: #f6f7fb; color: #2a2758; font-family: 'Inter', Arial, Helvetica, sans-serif; line-height: 1.6;">
    <div class="shell" style="font-family: 'Inter', Arial, Helvetica, sans-serif;">
        <table class="topbar" role="presentation" style="font-family: 'Inter', Arial, Helvetica, sans-serif;">
            <tr>
                <td align="left">
                    <img src="{{ $logoUrl }}" alt="Get Your Consultant" class="logo">
                </td>
                <td align="right" class="auto-message">
                    {{ $copy['autoMessage'] }}
                </td>
            </tr>
        </table>

        <div class="card">
            <div class="success-mark">&#10003;</div>
            <h1>{{ $trans['email_title'] ?? 'Your Property Report is Ready' }}</h1>
            <p class="lead">{{ $copy['intro'] }}</p>

            <div class="download-section">
                <p class="download-title">{{ $copy['downloadTitle'] }}</p>
                <p class="download-copy">{{ $copy['downloadBody'] }}</p>
            </div>

            <table class="support-box" role="presentation">
                <tr>
                    <td class="support-icon-cell">
                        <div class="support-icon-wrap">
                            <span class="support-icon-mark">?</span>
                        </div>
                    </td>
                    <td>
                        <p class="support-title">{{ $copy['supportTitle'] }}</p>
                        <p class="support-copy">{{ $copy['supportBody'] }}</p>
                    </td>
                    <td align="right" class="support-action">
                        <a href="{{ $contactUrl }}" class="support-link">{{ $copy['supportCta'] }}</a>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p class="footer-brand">{{ $copy['footerBrand'] }}</p>
            <p class="footer-desc">{{ $trans['landing_footer_desc'] ?? 'Clear residential buying and rental analysis, delivered fast in a structured format.' }}</p>

            <table class="footer-contact" role="presentation">
                <tr>
                    <td><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></td>
                    <td><a href="{{ $websiteUrl }}">{{ $websiteLabel }}</a></td>
                </tr>
            </table>

            <p class="footer-legal">{{ $trans['landing_footer_legal_note'] ?? 'GetYourConsultant trademark notice.' }}</p>
            <p class="footer-copy">&copy; {{ $currentYear }} {{ $copy['footerBrand'] }}. {{ $trans['landing_footer_copyright_suffix'] ?? 'All rights reserved.' }}</p>
        </div>
    </div>
</body>
</html>
