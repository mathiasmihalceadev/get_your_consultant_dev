<?php

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
    'supported' => ['en', 'ro'],
    'default' => env('APP_LOCALE', 'en'),
    'x_default_locale' => 'en',
    'domain_urls' => $domainUrls,
    'host_locale_map' => $hostLocaleMap,
    'translated_paths' => [
        '/' => [
            'en' => '/',
            'ro' => '/',
        ],
    ],
];