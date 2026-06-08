<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-footer.png') }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])
        @inertiaHead
        @unless (str_starts_with($page['component'], 'Admin/'))
            <x-marketing.google-tag />
        @endunless
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        @unless (str_starts_with($page['component'], 'Admin/'))
            <x-marketing.google-tag placement="body" />
        @endunless

        @inertia
        @unless (str_starts_with($page['component'], 'Admin/'))
            <x-marketing.cookie-banner />
        @endunless
    </body>
</html>
