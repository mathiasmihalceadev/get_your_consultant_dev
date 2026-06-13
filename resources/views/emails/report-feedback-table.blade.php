<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $copy['title'] }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f7fb; color:#20285f; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background-color:#f6f7fb;">
        <tr>
            <td align="center" style="padding:24px 12px 32px;">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="width:640px; max-width:640px; border-collapse:collapse;">
                    <tr>
                        <td align="left" style="padding:0 0 16px;">
                            <img src="{{ $logoUrl }}" width="190" alt="GetYourConsultant" style="display:block; width:190px; max-width:190px; height:auto; border:0; outline:none; text-decoration:none;">
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #e5e8f2; padding:34px 40px; font-family:Arial, Helvetica, sans-serif;">
                            <h1 style="margin:0 0 18px; padding:0; font-size:34px; line-height:38px; font-weight:700; color:#20285f;">
                                {{ $copy['title'] }}
                            </h1>

                            <p style="margin:0 0 14px; padding:0; font-size:15px; line-height:24px; color:#4f5785;">
                                {{ $copy['thanks'] }}
                            </p>
                            <p style="margin:0 0 14px; padding:0; font-size:15px; line-height:24px; color:#4f5785;">
                                {{ $copy['intro'] }}
                            </p>
                            <p style="margin:0 0 22px; padding:0; font-size:15px; line-height:24px; color:#4f5785;">
                                {{ $copy['prompt'] }}
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse;">
                                @foreach ($copy['questions'] as $question)
                                    <tr>
                                        <td style="padding:0 0 12px;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background-color:#f8f9ff; border:1px solid #e5e8f2;">
                                                <tr>
                                                    <td style="padding:14px 16px; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:22px; font-weight:700; color:#20285f;">
                                                        {{ $question['label'] }}
                                                        @if (!empty($question['hint']))
                                                            <br>
                                                            <span style="font-size:13px; line-height:19px; font-weight:400; color:#6f7595;">
                                                                {{ $question['hint'] }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse; margin-top:8px;">
                                <tr>
                                    <td bgcolor="#34306a" style="background-color:#34306a;">
                                        <a href="{{ $feedbackUrl }}" style="display:inline-block; padding:14px 22px; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:20px; font-weight:700; color:#ffffff; text-decoration:none;">
                                            {{ $copy['cta'] }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:18px 4px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#777da0;">
                            &copy; {{ $currentYear }} GetYourConsultant(TM).
                            <a href="{{ $websiteUrl }}" style="color:#5c638d; text-decoration:none;">{{ $copy['websiteLabel'] }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
