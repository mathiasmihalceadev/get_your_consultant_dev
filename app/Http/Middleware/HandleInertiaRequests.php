<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Support\LocalizedUrl;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $localizedUrls = LocalizedUrl::publicLocalizedUrlsForRequest($request);
        $activeLocale = app()->getLocale();
        $publicLocale = LocalizedUrl::publicLocale($activeLocale);
        $seoAlternates = [];

        if (isset($localizedUrls['en'])) {
            $seoAlternates['en'] = $localizedUrls['en'];
        }

        if (isset($localizedUrls['ro'])) {
            $seoAlternates['ro-RO'] = $localizedUrls['ro'];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'appFlags' => fn () => [
                'publicWizardMaintenance' => (bool) config('app.public_wizard_maintenance', false),
                'publicLocaleSwitcher' => (bool) config('app.public_locale_switcher', false),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'dataLayerEvents' => fn () => $request->session()->get('dataLayerEvents', []),
            ],
            'locale' => fn () => $activeLocale,
            'supportedLocales' => fn () => LocalizedUrl::supportedLocales(),
            'publicLocales' => fn () => LocalizedUrl::publicLocales(),
            'domainUrls' => fn () => LocalizedUrl::publicDomainUrls(),
            'localizedUrls' => fn () => $localizedUrls,
            'seoIndexing' => fn () => config('seo.indexing'),
            'seo' => fn () => [
                'canonical' => $localizedUrls[$publicLocale] ?? $request->url(),
                'alternates' => $seoAlternates,
                'xDefault' => $localizedUrls[LocalizedUrl::publicXDefaultLocale()] ?? null,
            ],
            'translations' => function () {
                $locale = app()->getLocale();
                $translationsPath = lang_path("{$locale}.json");
                return file_exists($translationsPath)
                    ? json_decode(file_get_contents($translationsPath), true)
                    : [];
            },
        ];
    }
}
