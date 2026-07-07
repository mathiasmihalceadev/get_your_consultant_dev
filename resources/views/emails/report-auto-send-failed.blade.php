<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Automatic report email failed</title>
</head>
<body style="margin:0;background:#f6f7fb;color:#34306a;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f7fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e3e5f2;">
                    <tr>
                        <td style="padding:24px;">
                            <h1 style="margin:0 0 16px;font-size:22px;line-height:1.25;color:#34306a;">Automatic report email failed</h1>

                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#56527c;">
                                The report was generated, but the automatic customer email could not be sent. The report was moved to <strong>to_be_sent</strong> so it can be handled manually.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:18px 0;border-collapse:collapse;">
                                <tr>
                                    <td style="padding:8px 0;font-size:13px;color:#777399;">Report ID</td>
                                    <td style="padding:8px 0;font-size:13px;color:#34306a;text-align:right;">#{{ $report->id }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;font-size:13px;color:#777399;">Customer email</td>
                                    <td style="padding:8px 0;font-size:13px;color:#34306a;text-align:right;">{{ $report->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;font-size:13px;color:#777399;">Report type</td>
                                    <td style="padding:8px 0;font-size:13px;color:#34306a;text-align:right;">{{ $report->report_type }}</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px;font-size:13px;font-weight:bold;color:#34306a;">Error</p>
                            <p style="margin:0 0 20px;padding:12px;background:#fff5f5;border:1px solid #f4cccc;font-size:13px;line-height:1.5;color:#7f1d1d;">
                                {{ $errorMessage }}
                            </p>

                            <p style="margin:0 0 20px;font-size:13px;line-height:1.6;color:#56527c;word-break:break-all;">
                                {{ $report->url }}
                            </p>

                            <a href="{{ $adminUrl }}" style="display:inline-block;background:#34306a;color:#ffffff;text-decoration:none;padding:12px 16px;font-size:14px;font-weight:bold;">
                                Open report in admin
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
