<!DOCTYPE html>
<html lang="{{ $isRomanian ? 'ro' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isRomanian ? 'Factura de test este gata' : 'Your test invoice is ready' }}</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; color:#20285f; font-family:Arial, Helvetica, sans-serif; line-height:1.6;">
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e5e8f2; padding:32px;">
        @if ($isRomanian)
            <p style="margin-top:0;">Bună,</p>
            <p>
                Acesta este un email de test generat din fluxul intern Stripe + SmartBill.
                Factura SmartBill este atașată acestui mesaj.
            </p>
            <p>
                Pentru acest flux nu se generează PDF-ul final al raportului.
            </p>
            <p>
                Document: <strong>{{ $documentNumber }}</strong><br>
                Email folosit în checkout: <strong>{{ $recipientEmail ?: '—' }}</strong>
            </p>
            <p style="margin-bottom:0;">Mesaj automat de test.</p>
        @else
            <p style="margin-top:0;">Hello,</p>
            <p>
                This is a test email generated from the internal Stripe + SmartBill flow.
                The SmartBill invoice is attached to this message.
            </p>
            <p>
                This flow does not generate the final report PDF.
            </p>
            <p>
                Document: <strong>{{ $documentNumber }}</strong><br>
                Checkout email: <strong>{{ $recipientEmail ?: '—' }}</strong>
            </p>
            <p style="margin-bottom:0;">Automated test message.</p>
        @endif
    </div>
</body>
</html>