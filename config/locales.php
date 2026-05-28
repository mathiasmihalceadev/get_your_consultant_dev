<?php

$parseLocaleList = static function (?string $value, array $fallback): array {
    $source = $value;

    if (!is_string($source) || trim($source) === '') {
        return array_values(array_unique($fallback));
    }

    $locales = array_filter(array_map(
        static fn (string $locale): string => strtolower(trim($locale)),
        explode(',', $source),
    ));

    return array_values(array_unique($locales));
};

$supportedLocales = $parseLocaleList(env('APP_SUPPORTED_LOCALES'), ['en', 'ro']);
$publicLocales = array_values(array_filter(
    $parseLocaleList(env('APP_PUBLIC_LOCALES'), $supportedLocales),
    static fn (string $locale): bool => in_array($locale, $supportedLocales, true),
));

if ($publicLocales === []) {
    $publicLocales = $supportedLocales;
}

$domainUrls = [
    'en' => env('APP_DOMAIN_EN', 'http://myapp-com.test:8000'),
    'ro' => env('APP_DOMAIN_RO', 'http://myapp-ro.test:8000'),
];

$hostLocaleMap = [];
$defaultHosts = [
    'en' => ['myapp-com.test', 'stage.example.com', 'example.com', 'www.example.com'],
    'ro' => ['myapp-ro.test', 'stage.example.ro', 'example.ro', 'www.example.ro'],
];

foreach ($defaultHosts as $locale => $hosts) {
    foreach ($hosts as $host) {
        $hostLocaleMap[$host] = $locale;
    }

    $configuredHost = parse_url($domainUrls[$locale] ?? '', PHP_URL_HOST);

    if (is_string($configuredHost) && $configuredHost !== '') {
        $hostLocaleMap[strtolower($configuredHost)] = $locale;
    }
}

return [
    'supported' => $supportedLocales,
    'public' => $publicLocales,
    'default' => env('APP_LOCALE', 'en'),
    'x_default_locale' => env('APP_X_DEFAULT_LOCALE', env('APP_LOCALE', 'en')),
    'domain_urls' => $domainUrls,
    'host_locale_map' => $hostLocaleMap,
    'translated_paths' => [
        '/' => [
            'en' => '/',
            'ro' => '/',
        ],
        '/privacy-policy' => [
            'en' => '/privacy-policy',
            'ro' => '/politica-de-confidentialitate',
        ],
        '/terms-and-conditions' => [
            'en' => '/terms-and-conditions',
            'ro' => '/termeni-si-conditii',
        ],
        '/cookie-policy' => [
            'en' => '/cookie-policy',
            'ro' => '/politica-de-cookie-uri',
        ],
    ],
];