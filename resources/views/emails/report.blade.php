<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #111; line-height: 1.6; background: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 8px; padding: 40px; border: 1px solid #e5e7eb; }
        h1 { font-size: 22px; color: #0a0a0a; margin: 0 0 16px; }
        p { font-size: 14px; color: #444; margin: 0 0 12px; }
        .badge { display: inline-block; background: #303048; color: #fff; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .link { color: #0073F0; text-decoration: none; }
        .footer { margin-top: 30px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">{{ $typeLabel }}</span>
        <h1 style="margin-top: 16px">{{ $trans['email_title'] ?? 'Your Property Report is Ready' }}</h1>

        <p>{{ $trans['email_body'] ?? 'Your report for the following property is attached:' }}</p>
        <p style="word-break: break-all; color: #0073F0; font-size: 13px;">{{ $report->url }}</p>

        <div class="footer">
            <p>{{ $trans['email_footer'] ?? 'Get Your Consultant' }}</p>
        </div>
    </div>
</body>
</html>
