<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $copy['subject'] }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7fb; color:#1f2a44; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background-color:#f4f7fb;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="width:640px; max-width:640px; border-collapse:collapse; background-color:#ffffff; border:1px solid #d9e2f0;">
                    <tr>
                        <td style="padding:28px 28px 20px; font-family:Arial, Helvetica, sans-serif;">
                            <h1 style="margin:0 0 10px; padding:0; font-size:24px; line-height:30px; font-weight:700; color:#222b45;">
                                {{ $copy['title'] }}
                            </h1>
                            <p style="margin:0 0 20px; padding:0; font-size:14px; line-height:24px; color:#49566e;">
                                {{ $copy['body'] }}
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td width="150" style="width:150px; padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; font-weight:700; color:#222b45;">{{ $copy['customer'] }}</td>
                                    <td style="padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#49566e;">{{ $report->email }}</td>
                                </tr>
                                <tr>
                                    <td width="150" style="width:150px; padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; font-weight:700; color:#222b45;">{{ $copy['type'] }}</td>
                                    <td style="padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#49566e;">{{ $reportTypeLabel }}</td>
                                </tr>
                                <tr>
                                    <td width="150" valign="top" style="width:150px; padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; font-weight:700; color:#222b45;">{{ $copy['listing'] }}</td>
                                    <td style="padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#49566e; word-break:break-word;">{{ $report->url }}</td>
                                </tr>
                                @if ($processedAt)
                                    <tr>
                                        <td width="150" style="width:150px; padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; font-weight:700; color:#222b45;">{{ $copy['processed_at'] }}</td>
                                        <td style="padding:8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#49566e;">{{ $processedAt }}</td>
                                    </tr>
                                @endif
                            </table>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse; margin-top:24px;">
                                <tr>
                                    <td bgcolor="#34306a" style="background-color:#34306a;">
                                        <a href="{{ $adminUrl }}" style="display:inline-block; padding:12px 18px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; font-weight:700; color:#ffffff; text-decoration:none;">{{ $copy['cta'] }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
