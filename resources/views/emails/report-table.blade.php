@php
    $locale = strtolower((string) ($report->locale ?? 'en'));
    $isRomanian = $locale === 'ro';
    $copy = [
        'autoMessage' => $isRomanian
            ? 'Mesaj automat - te rugăm să nu răspunzi direct la acest email.'
            : 'Automated message - please do not reply directly to this email.',
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
        'supportTitle' => $isRomanian ? 'Întrebări sau nelmuriri?' : 'Questions or anything unclear?',
        'supportBody' => $isRomanian
            ? 'Răspundem rapid la orice întrebare.'
            : 'We reply quickly to any question.',
        'supportCta' => $isRomanian ? 'Contactează-ne' : 'Contact us',
        'footerBrand' => 'GetYourConsultant',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $trans['email_title'] ?? 'Your Property Report is Ready' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f7fb; color:#20285f; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background-color:#f6f7fb;">
        <tr>
            <td align="center" style="padding:24px 12px 32px;">
                <table role="presentation" width="680" cellspacing="0" cellpadding="0" border="0" style="width:680px; max-width:680px; border-collapse:collapse;">
                    <tr>
                        <td align="left" style="padding:0 0 16px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td align="left" valign="middle" style="padding:0;">
                                        <img src="{{ $logoUrl }}" width="190" alt="Get Your Consultant" style="display:block; width:190px; max-width:190px; height:auto; border:0; outline:none; text-decoration:none;">
                                    </td>
                                    <td align="right" valign="middle" style="padding:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#6f7595;">
                                        {{ $copy['autoMessage'] }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #e5e8f2; padding:36px 42px 34px; font-family:Arial, Helvetica, sans-serif;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td align="left" style="padding:0 0 18px;">
                                        <table role="presentation" width="56" height="56" cellspacing="0" cellpadding="0" border="0" style="width:56px; height:56px; border-collapse:collapse;">
                                            <tr>
                                                <td align="center" valign="middle" style="width:56px; height:56px; background-color:#e6f7eb; color:#34a853; font-size:34px; line-height:56px; font-family:Arial, Helvetica, sans-serif;">
                                                    &#10003;
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0;">
                                        <h1 style="margin:0 0 12px; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:32px; line-height:36px; font-weight:700; color:#20285f;">
                                            {{ $trans['email_title'] ?? 'Your Property Report is Ready' }}
                                        </h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 26px; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:26px; color:#343b72;">
                                        {{ $copy['intro'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e9ebf4; padding:24px 0 0;">
                                        <p style="margin:0 0 6px; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:22px; font-weight:700; color:#20285f;">
                                            {{ $copy['downloadTitle'] }}
                                        </p>
                                        <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:23px; color:#4f5785;">
                                            {{ $copy['downloadBody'] }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:28px 0 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background-color:#eef3ff;">
                                            <tr>
                                                <td width="58" valign="middle" style="width:58px; padding:18px 0 18px 20px;">
                                                    <table role="presentation" width="42" height="42" cellspacing="0" cellpadding="0" border="0" style="width:42px; height:42px; border-collapse:collapse;">
                                                        <tr>
                                                            <td align="center" valign="middle" style="width:42px; height:42px; border:2px solid #4052cf; color:#4052cf; font-size:22px; line-height:38px; font-weight:700; font-family:Arial, Helvetica, sans-serif;">
                                                                ?
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="middle" style="padding:18px 20px; font-family:Arial, Helvetica, sans-serif;">
                                                    <p style="margin:0 0 4px; padding:0; font-size:14px; line-height:20px; font-weight:700; color:#20285f;">
                                                        {{ $copy['supportTitle'] }}
                                                    </p>
                                                    <p style="margin:0; padding:0; font-size:14px; line-height:22px; color:#4f5785;">
                                                        {{ $copy['supportBody'] }}
                                                    </p>
                                                </td>
                                                <td align="right" valign="middle" style="padding:18px 20px 18px 0; font-family:Arial, Helvetica, sans-serif;">
                                                    <a href="{{ $contactUrl }}" style="font-size:14px; line-height:20px; font-weight:700; color:#4052cf; text-decoration:underline;">
                                                        {{ $copy['supportCta'] }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:22px 4px 0; font-family:Arial, Helvetica, sans-serif;">
                            <p style="margin:0; padding:0; font-size:18px; line-height:25px; font-weight:700; color:#20285f;">
                                {{ $copy['footerBrand'] }}
                            </p>
                            <p style="margin:8px auto 0; padding:0; max-width:540px; font-size:13px; line-height:21px; color:#5c638d;">
                                {{ $trans['landing_footer_desc'] ?? 'Clear residential buying and rental analysis, delivered fast in a structured format.' }}
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; margin-top:16px;">
                                <tr>
                                    <td align="center" style="padding:8px 10px; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#5c638d;">
                                        <a href="mailto:{{ $contactEmail }}" style="color:#5c638d; text-decoration:none;">{{ $contactEmail }}</a>
                                    </td>
                                    <td align="center" style="padding:8px 10px; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#5c638d;">
                                        <a href="{{ $websiteUrl }}" style="color:#5c638d; text-decoration:none;">{{ $websiteLabel }}</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:10px 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#777da0;">
                                {{ $trans['landing_footer_legal_note'] ?? 'GetYourConsultant trademark notice.' }}
                            </p>
                            <p style="margin:6px 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#777da0;">
                                &copy; {{ $currentYear }} {{ $copy['footerBrand'] }}. {{ $trans['landing_footer_copyright_suffix'] ?? 'All rights reserved.' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
