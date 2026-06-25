@props([
    'placement' => 'head',
])

@php
    $containerId = config('services.google_tag_manager.container_id');
    $consentCookie = request()->cookie('gyc_cookie_consent');
    $cookiePreferences = json_decode((string) request()->cookie('gyc_cookie_preferences', ''), true);
    $cookiePreferences = is_array($cookiePreferences) ? $cookiePreferences : [];
    $hasAcceptedAll = $consentCookie === 'accepted';
    $statisticsConsent = $hasAcceptedAll || (bool) ($cookiePreferences['statistics'] ?? false);
    $marketingConsent = $hasAcceptedAll || (bool) ($cookiePreferences['marketing'] ?? false);
    $preferencesConsent = $hasAcceptedAll || (bool) ($cookiePreferences['preferences'] ?? false);
    $googleConsentDefaults = [
        'ad_storage' => $marketingConsent ? 'granted' : 'denied',
        'ad_user_data' => $marketingConsent ? 'granted' : 'denied',
        'ad_personalization' => $marketingConsent ? 'granted' : 'denied',
        'analytics_storage' => $statisticsConsent ? 'granted' : 'denied',
        'functionality_storage' => $preferencesConsent ? 'granted' : 'denied',
        'personalization_storage' => $preferencesConsent ? 'granted' : 'denied',
        'security_storage' => 'granted',
        'wait_for_update' => 500,
    ];
@endphp

@if ($containerId && $placement === 'head')
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {!! json_encode($googleConsentDefaults, JSON_UNESCAPED_SLASHES) !!});
        gtag('set', 'ads_data_redaction', true);
    </script>
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $containerId }}');
    </script>
@endif

@if ($containerId && $placement === 'body')
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ $containerId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
@endif
