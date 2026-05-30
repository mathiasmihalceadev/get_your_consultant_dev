<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <title>{{ $copy['subject'] }}</title>
    </head>
    <body style="margin:0;padding:24px;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2a44;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #d9e2f0;">
            <tr>
                <td style="padding:28px 28px 20px;">
                    <h1 style="margin:0 0 10px;font-size:24px;line-height:1.2;color:#222b45;">{{ $copy['title'] }}</h1>
                    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#49566e;">{{ $copy['body'] }}</p>

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;line-height:1.7;">
                        <tr>
                            <td style="padding:8px 0;font-weight:700;color:#222b45;width:150px;">{{ $copy['customer'] }}</td>
                            <td style="padding:8px 0;color:#49566e;">{{ $report->email }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;font-weight:700;color:#222b45;">{{ $copy['type'] }}</td>
                            <td style="padding:8px 0;color:#49566e;">{{ $reportTypeLabel }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;font-weight:700;color:#222b45;">{{ $copy['listing'] }}</td>
                            <td style="padding:8px 0;color:#49566e;word-break:break-word;">{{ $report->url }}</td>
                        </tr>
                        @if ($processedAt)
                            <tr>
                                <td style="padding:8px 0;font-weight:700;color:#222b45;">{{ $copy['processed_at'] }}</td>
                                <td style="padding:8px 0;color:#49566e;">{{ $processedAt }}</td>
                            </tr>
                        @endif
                    </table>

                    <p style="margin:24px 0 0;">
                        <a href="{{ $adminUrl }}" style="display:inline-block;background:#34306a;color:#ffffff;text-decoration:none;padding:12px 18px;font-size:14px;font-weight:700;">{{ $copy['cta'] }}</a>
                    </p>
                </td>
            </tr>
        </table>
    </body>
</html>