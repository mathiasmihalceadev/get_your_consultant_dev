<?php

namespace App\Support;

use Illuminate\Http\Request;

class LocalizedUrl
{
    public static function supportedLocales(): array
    {
        return config('locales.supported', ['en', 'ro']);
    }

    public static function publicLocales(): array
    {
        $configured = config('locales.public', static::supportedLocales());

        if (!is_array($configured) || $configured === []) {
            return static::supportedLocales();
        }

        $publicLocales = array_values(array_filter(
            array_map(static fn (mixed $locale): string => strtolower((string) $locale), $configured),
            static fn (string $locale): bool => in_array($locale, static::supportedLocales(), true),
        ));

        return $publicLocales !== [] ? $publicLocales : static::supportedLocales();
    }

    public static function defaultLocale(): string
    {
        return config('locales.default', config('app.locale', 'en'));
    }

    public static function defaultPublicLocale(): string
    {
        $publicLocales = static::publicLocales();
        $preferredLocale = strtolower((string) config('locales.x_default_locale', static::defaultLocale()));

        if (in_array($preferredLocale, $publicLocales, true)) {
            return $preferredLocale;
        }

        return $publicLocales[0] ?? static::defaultLocale();
    }

    public static function publicXDefaultLocale(): string
    {
        return static::defaultPublicLocale();
    }

    public static function domainUrls(): array
    {
        return config('locales.domain_urls', []);
    }

    public static function publicDomainUrls(): array
    {
        return array_filter(
            static::domainUrls(),
            static fn (mixed $url, string $locale): bool => is_string($url)
                && in_array(strtolower($locale), static::publicLocales(), true),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    public static function isPublicLocale(string $locale): bool
    {
        return in_array(strtolower($locale), static::publicLocales(), true);
    }

    public static function publicLocale(string $locale): string
    {
        $normalizedLocale = strtolower($locale);

        return static::isPublicLocale($normalizedLocale)
            ? $normalizedLocale
            : static::defaultPublicLocale();
    }

    public static function localeForHost(?string $host): string
    {
        $normalizedHost = strtolower((string) $host);
        $hostLocaleMap = config('locales.host_locale_map', []);

        return $hostLocaleMap[$normalizedHost] ?? static::defaultLocale();
    }

    public static function requestHost(Request $request): string
    {
        $host = $request->headers->get('host')
            ?? $request->server('HTTP_HOST')
            ?? $request->getHttpHost()
            ?? $request->getHost();

        return strtolower(explode(':', (string) $host)[0]);
    }

    public static function localizedUrlsForRequest(Request $request, ?array $locales = null): array
    {
        $requestUri = $request->getRequestUri() ?: '/';
        $resolvedLocales = $locales ?? static::supportedLocales();

        return collect($resolvedLocales)
            ->mapWithKeys(fn (string $locale) => [$locale => static::urlForLocale($locale, $requestUri)])
            ->all();
    }

    public static function publicLocalizedUrlsForRequest(Request $request): array
    {
        return static::localizedUrlsForRequest($request, static::publicLocales());
    }

    public static function urlForLocale(string $locale, string $path = '/'): string
    {
        $baseUrl = rtrim((string) config("locales.domain_urls.{$locale}", config('app.url')), '/');
        $localizedPath = static::equivalentPath($path, $locale);

        return $baseUrl.($localizedPath === '/' ? '' : $localizedPath);
    }

    public static function publicUrlForLocale(string $locale, string $path = '/'): string
    {
        return static::urlForLocale(static::publicLocale($locale), $path);
    }

    public static function equivalentPath(string $path, string $targetLocale): string
    {
        $parsedUrl = parse_url($path);
        $pathOnly = $parsedUrl['path'] ?? '/';
        $normalizedPath = '/'.ltrim($pathOnly, '/');

        if ($normalizedPath === '//') {
            $normalizedPath = '/';
        }

        $canonicalPath = static::canonicalizePath($normalizedPath);
        $translatedPath = config("locales.translated_paths.{$canonicalPath}.{$targetLocale}");
        $resolvedPath = is_string($translatedPath) ? $translatedPath : $normalizedPath;

        $query = isset($parsedUrl['query']) && $parsedUrl['query'] !== '' ? '?'.$parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) && $parsedUrl['fragment'] !== '' ? '#'.$parsedUrl['fragment'] : '';

        return $resolvedPath.$query.$fragment;
    }

    protected static function canonicalizePath(string $path): string
    {
        foreach (config('locales.translated_paths', []) as $canonical => $translations) {
            foreach ($translations as $translatedPath) {
                if (!is_string($translatedPath)) {
                    continue;
                }

                if (rtrim($translatedPath, '/') === rtrim($path, '/')) {
                    return $canonical;
                }

                if ($translatedPath === '/' && $path === '/') {
                    return $canonical;
                }
            }
        }

        return $path;
    }
}