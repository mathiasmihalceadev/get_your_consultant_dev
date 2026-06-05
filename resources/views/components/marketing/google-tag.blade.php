@php
    $googleTagId = config('services.google.tag_id');
    $cookiePreferences = json_decode((string) request()->cookie('gyc_cookie_preferences', ''), true);
    $hasMarketingConsent = is_array($cookiePreferences)
        && filter_var($cookiePreferences['marketing'] ?? false, FILTER_VALIDATE_BOOLEAN);
@endphp

@if ($googleTagId && $hasMarketingConsent)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleTagId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '{{ $googleTagId }}');
    </script>
@endif