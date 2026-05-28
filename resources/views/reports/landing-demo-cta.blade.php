@php
    $demoLocale = strtolower((string) ($locale ?? 'en')) === 'ro' ? 'ro' : 'en';
    $copy = $demoLocale === 'ro'
        ? [
            'title' => 'Generează o analiză completă',
            'button' => 'Obține raport',
        ]
        : [
            'title' => 'Generate a complete analysis',
            'button' => 'Get report',
        ];

    $logoPath = public_path('images/main-logo-transparent.png');
    $logoDataUri = '';

    if (is_file($logoPath)) {
        $binary = file_get_contents($logoPath);
        if ($binary !== false) {
            $logoDataUri = 'data:image/png;base64,' . base64_encode($binary);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $demoLocale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $copy['title'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', Arial, Helvetica, sans-serif;
            color: #20285f;
            background: #eff3ff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(113, 128, 217, 0.22), transparent 30%),
                radial-gradient(circle at bottom left, rgba(52, 48, 106, 0.12), transparent 34%),
                linear-gradient(180deg, #f7f9ff 0%, #edf2ff 52%, #f7f9ff 100%);
        }

        .shape {
            position: absolute;
            border-radius: 999px;
            filter: blur(10px);
            opacity: 0.7;
        }

        .shape-one {
            width: 180mm;
            height: 48mm;
            top: 18mm;
            left: 18mm;
            background: rgba(216, 222, 248, 0.82);
        }

        .shape-two {
            width: 130mm;
            height: 42mm;
            bottom: 26mm;
            right: -10mm;
            background: rgba(69, 65, 138, 0.12);
        }

        .frame {
            position: relative;
            z-index: 2;
            padding: 18mm 18mm 16mm;
        }

        .brand-row {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12mm;
        }

        .logo {
            width: 70mm;
            height: auto;
        }

        .preview-shell {
            position: relative;
            margin-top: 6mm;
            min-height: 204mm;
        }

        .blurred-preview {
            position: absolute;
            inset: 0;
            padding: 8mm 4mm 0;
            filter: blur(7px);
            opacity: 0.84;
        }

        .preview-stack {
            display: flex;
            flex-direction: column;
            gap: 6mm;
        }

        .preview-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(52, 48, 106, 0.09);
            border-radius: 8mm;
            box-shadow: 0 8mm 16mm rgba(52, 48, 106, 0.06);
            padding: 7mm;
        }

        .preview-card.tall {
            min-height: 84mm;
        }

        .preview-row {
            display: flex;
            gap: 4mm;
            margin-bottom: 4mm;
        }

        .preview-pill,
        .preview-line,
        .preview-box {
            background: linear-gradient(90deg, rgba(52, 48, 106, 0.11), rgba(78, 89, 183, 0.16));
            border-radius: 999px;
        }

        .preview-pill {
            height: 7mm;
        }

        .preview-line {
            height: 4.5mm;
            margin-top: 3mm;
        }

        .preview-box {
            height: 26mm;
            border-radius: 5mm;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
            margin-top: 5mm;
        }

        .overlay {
            position: relative;
            z-index: 3;
            width: 100%;
            min-height: 204mm;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8mm 0 0;
        }

        .cta-card {
            width: 100%;
            max-width: 118mm;
            border-radius: 0;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(52, 48, 106, 0.09);
            box-shadow: 0 10mm 22mm rgba(52, 48, 106, 0.12);
            padding: 12mm 10mm;
            text-align: center;
        }

        .cta-title {
            margin: 0;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #20285f;
        }

        .cta-button {
            display: inline-block;
            margin-top: 7mm;
            padding: 5mm 10mm;
            border-radius: 0;
            background: #34306a;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 6mm 12mm rgba(52, 48, 106, 0.2);
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="shape shape-one"></div>
        <div class="shape shape-two"></div>

        <div class="frame">
            <div class="brand-row">
                @if($logoDataUri !== '')
                    <img class="logo" src="{{ $logoDataUri }}" alt="Get Your Consultant">
                @endif
            </div>

            <div class="preview-shell">
                <div class="blurred-preview">
                    <div class="preview-stack">
                        <div class="preview-card tall">
                            <div class="preview-row">
                                <div class="preview-pill" style="width: 34mm;"></div>
                                <div class="preview-pill" style="width: 22mm;"></div>
                            </div>
                            <div class="preview-line" style="width: 100%;"></div>
                            <div class="preview-line" style="width: 82%;"></div>
                            <div class="preview-grid">
                                <div class="preview-box"></div>
                                <div class="preview-box"></div>
                                <div class="preview-box"></div>
                                <div class="preview-box"></div>
                            </div>
                        </div>

                        <div class="preview-card">
                            <div class="preview-row">
                                <div class="preview-pill" style="width: 44mm;"></div>
                            </div>
                            <div class="preview-line" style="width: 92%;"></div>
                            <div class="preview-line" style="width: 96%;"></div>
                            <div class="preview-line" style="width: 76%;"></div>
                        </div>
                    </div>
                </div>

                <div class="overlay">
                    <div class="cta-card">
                        <h1 class="cta-title">{{ $copy['title'] }}</h1>

                        <a class="cta-button" href="{{ $ctaUrl }}">{{ $copy['button'] }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>