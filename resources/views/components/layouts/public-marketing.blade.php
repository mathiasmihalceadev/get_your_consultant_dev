@props([
    'title',
    'description',
    'canonical' => null,
    'alternates' => [],
    'xDefault' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-footer.png') }}">
        <title>{{ $title }}</title>
        <meta name="description" content="{{ $description }}">
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Get Your Consultant">
        <meta property="og:locale" content="{{ app()->getLocale() === 'ro' ? 'ro_RO' : 'en_US' }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title }}">
        <meta name="twitter:description" content="{{ $description }}">
        @if ($canonical)
            <link rel="canonical" href="{{ $canonical }}">
            <meta property="og:url" content="{{ $canonical }}">
        @endif
        @foreach ($alternates as $hrefLang => $href)
            <link rel="alternate" hrefLang="{{ $hrefLang }}" href="{{ $href }}">
        @endforeach
        @if ($xDefault)
            <link rel="alternate" hrefLang="x-default" href="{{ $xDefault }}">
        @endif
        @vite('resources/css/app.css')
        <x-marketing.google-tag />
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <x-marketing.google-tag placement="body" />

        <div class="min-h-screen flex flex-col bg-white">
            <div class="h-0.75 bg-brand-secondary"></div>
            <x-marketing.header />

            <main class="flex flex-1 flex-col">
                {{ $slot }}
            </main>

            <x-marketing.footer />
        </div>

        <x-marketing.cookie-banner />
        <x-marketing.data-layer-events :events="session('dataLayerEvents', [])" />
    </body>
</html>