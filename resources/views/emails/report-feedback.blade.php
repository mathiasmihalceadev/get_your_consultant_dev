<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $copy['title'] }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f6f7fb;
            color: #20285f;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }

        .shell {
            max-width: 680px;
            margin: 0 auto;
            padding: 24px 16px 32px;
        }

        .logo {
            display: block;
            height: 52px;
            width: auto;
            margin-bottom: 16px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e8f2;
            border-radius: 0;
            padding: 34px 40px;
            box-shadow: 0 20px 44px rgba(52, 48, 106, 0.06);
        }

        h1 {
            margin: 0 0 18px;
            font-size: 38px;
            line-height: 1.08;
            color: #20285f;
        }

        p {
            margin: 0 0 14px;
            font-size: 15px;
            color: rgba(32, 40, 95, 0.78);
        }

        .questions {
            margin: 22px 0;
            padding: 0;
            list-style: none;
        }

        .questions li {
            margin: 0 0 12px;
            padding: 14px 16px;
            border: 1px solid #e5e8f2;
            border-radius: 0;
            background: #f8f9ff;
            color: #20285f;
            font-size: 15px;
            font-weight: 700;
        }

        .hint {
            display: block;
            margin-top: 3px;
            font-size: 13px;
            font-weight: 400;
            color: rgba(32, 40, 95, 0.62);
        }

        .button {
            display: inline-block;
            margin-top: 8px;
            padding: 14px 22px;
            border-radius: 0;
            background: #34306a;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
        }

        .footer {
            padding-top: 18px;
            text-align: center;
            font-size: 12px;
            color: rgba(32, 40, 95, 0.56);
        }

        @media only screen and (max-width: 640px) {
            .card {
                padding: 26px 20px;
            }

            h1 {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <img src="{{ $logoUrl }}" alt="GetYourConsultant" class="logo">

        <div class="card">
            <h1>{{ $copy['title'] }}</h1>

            <p>{{ $copy['thanks'] }}</p>
            <p>{{ $copy['intro'] }}</p>
            <p>{{ $copy['prompt'] }}</p>

            <ul class="questions">
                @foreach ($copy['questions'] as $question)
                    <li>
                        {{ $question['label'] }}
                        @if (!empty($question['hint']))
                            <span class="hint">{{ $question['hint'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <a href="{{ $feedbackUrl }}" class="button">{{ $copy['cta'] }}</a>
        </div>

        <div class="footer">
            &copy; {{ $currentYear }} GetYourConsultant™.
            <a href="{{ $websiteUrl }}" style="color:rgba(32,40,95,0.72);">{{ $copy['websiteLabel'] }}</a>
        </div>
    </div>
</body>
</html>
