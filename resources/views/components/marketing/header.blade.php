@php
    use App\Support\LocalizedUrl;

    $locale = app()->getLocale();
    $homeUrl = LocalizedUrl::publicUrlForLocale($locale, '/');
    $navItems = [
        ['label' => __('about'), 'href' => LocalizedUrl::publicUrlForLocale($locale, '/about-us')],
        ['label' => __('landing_nav_example'), 'href' => $homeUrl.'#report-example'],
        ['label' => __('how_it_works'), 'href' => $homeUrl.'#how-it-works'],
        ['label' => __('landing_nav_reviews'), 'href' => $homeUrl.'#reviews'],
        ['label' => __('pricing'), 'href' => $homeUrl.'#pricing'],
        ['label' => __('contact'), 'href' => LocalizedUrl::publicUrlForLocale($locale, '/contact')],
    ];
    $showLocaleSwitcher = (bool) config('app.public_locale_switcher', false) && count(LocalizedUrl::publicLocales()) > 1;
    $otherLocale = collect(LocalizedUrl::publicLocales())->first(fn (string $publicLocale) => $publicLocale !== $locale);
    $localizedUrls = LocalizedUrl::publicLocalizedUrlsForRequest(request());
@endphp

<header class="sticky top-0 z-50 border-b border-gray-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 lg:gap-6">
            <a href="{{ $homeUrl }}" class="shrink-0 flex items-center gap-2.5 text-lg font-bold text-brand-primary transition-opacity hover:opacity-80">
                <img class="h-10 w-auto object-contain md:h-12" src="{{ asset('images/logo-white.jpg') }}" alt="{{ __('site_name') }}">
            </a>

            <nav class="hidden min-w-0 flex-1 items-center justify-center gap-1 px-2 lg:flex">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" class="truncate px-3 py-1.5 text-[14px] font-semibold text-brand-primary/76 transition-colors hover:bg-[#eef1ff] hover:text-brand-primary md:text-base">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="shrink-0 flex items-center gap-2.5">
                <a href="{{ LocalizedUrl::publicUrlForLocale($locale, '/get-report') }}" class="inline-flex items-center justify-center bg-brand-primary px-4 py-2 text-[14px] font-semibold text-white transition-colors hover:bg-brand-primary/90 md:text-base">
                    {{ __('get_report') }}
                </a>

                @if ($showLocaleSwitcher && $otherLocale && isset($localizedUrls[$otherLocale]))
                    <a href="{{ $localizedUrls[$otherLocale] }}" data-locale-switcher aria-label="{{ __('language_switch_to', ['language' => strtoupper($otherLocale)]) }}" class="flex items-center gap-1.5 border border-gray-200 px-3 py-2 text-[14px] font-semibold text-brand-primary/76 transition-colors hover:text-brand-primary md:text-base">
                        <x-marketing.icon name="globe" class="h-4 w-4 shrink-0" />
                        {{ strtoupper($otherLocale) }}
                    </a>
                @endif
            </div>
        </div>

        <nav class="mt-3 -mx-1 flex items-center gap-1 overflow-x-auto pb-1 lg:hidden">
            @foreach ($navItems as $item)
                <a href="{{ $item['href'] }}" class="shrink-0 px-3 py-1.5 text-[14px] font-semibold text-brand-primary/76 transition-colors hover:bg-[#eef1ff] hover:text-brand-primary md:text-base">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>

<script>
    document.querySelectorAll('[data-locale-switcher]').forEach((link) => {
        if (window.location.hash) {
            link.href = `${link.href}${window.location.hash}`;
        }
    });
</script>
