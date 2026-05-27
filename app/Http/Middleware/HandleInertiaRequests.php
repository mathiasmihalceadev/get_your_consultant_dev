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
        $localizedUrls = LocalizedUrl::localizedUrlsForRequest($request);
        $activeLocale = app()->getLocale();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'appFlags' => fn () => [
                'publicWizardMaintenance' => (bool) config('app.public_wizard_maintenance', false),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'locale' => fn () => $activeLocale,
            'supportedLocales' => fn () => LocalizedUrl::supportedLocales(),
            'domainUrls' => fn () => LocalizedUrl::domainUrls(),
            'localizedUrls' => fn () => $localizedUrls,
            'seoIndexing' => fn () => config('seo.indexing'),
            'seo' => fn () => [
                'canonical' => $localizedUrls[$activeLocale] ?? $request->url(),
                'alternates' => [
                    'en' => $localizedUrls['en'] ?? null,
                    'ro-RO' => $localizedUrls['ro'] ?? null,
                ],
                'xDefault' => $localizedUrls[config('locales.x_default_locale', 'en')] ?? null,
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
