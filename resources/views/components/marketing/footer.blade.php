@php
    use App\Support\LocalizedUrl;

    $locale = app()->getLocale();
    $legalPaths = [
        'privacy' => LocalizedUrl::equivalentPath('/privacy-policy', $locale),
        'terms' => LocalizedUrl::equivalentPath('/terms-and-conditions', $locale),
        'cookies' => LocalizedUrl::equivalentPath('/cookie-policy', $locale),
    ];
@endphp

<footer class="bg-brand-primary">
    <div class="h-0.75 bg-brand-secondary"></div>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <img src="{{ asset('images/logo-footer.png') }}" alt="{{ __('site_name') }}" class="mb-4 h-18 w-auto object-contain md:h-22">
                <p class="max-w-md text-[14px] text-white/80 md:text-base">{{ __('landing_footer_desc') }}</p>
            </div>

            <div>
                <h4 class="mb-3 text-[14px] text-white/80 md:text-base">{{ __('footer_follow_us') }}</h4>
                <div class="flex items-center gap-2">
                    <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white">
                        <x-marketing.icon name="facebook-logo" weight="duotone" class="h-[18px] w-[18px]" />
                    </a>
                    <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white">
                        <x-marketing.icon name="instagram-logo" weight="duotone" class="h-[18px] w-[18px]" />
                    </a>
                    <a href="https://www.tiktok.com" target="_blank" rel="noopener noreferrer" aria-label="TikTok" class="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white">
                        <x-marketing.icon name="tiktok-logo" weight="duotone" class="h-[18px] w-[18px]" />
                    </a>
                </div>
                <div class="mt-4">
                    <p class="whitespace-pre-line text-[14px] leading-[1.7] text-white/70 md:text-base">{{ __('landing_footer_address') }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4 border-t border-white/10 pt-6 md:flex-row md:items-end md:justify-between">
            <div class="flex flex-wrap gap-4 text-[14px] md:text-base">
                <a href="{{ url('/').'#hero-form' }}" class="text-white/70 transition-colors hover:text-white">{{ __('get_report') }}</a>
                <a href="{{ url($legalPaths['privacy']) }}" class="text-white/70 transition-colors hover:text-white">{{ __('privacy') }}</a>
                <a href="{{ url($legalPaths['terms']) }}" class="text-white/70 transition-colors hover:text-white">{{ __('terms') }}</a>
                <a href="{{ url($legalPaths['cookies']) }}" class="text-white/70 transition-colors hover:text-white">{{ __('cookie_policy') }}</a>
                <a href="{{ route('contact') }}" class="text-white/70 transition-colors hover:text-white">{{ __('contact') }}</a>
            </div>

            <div class="space-y-1 text-[13px] leading-[1.55] text-white/56 md:ml-auto md:max-w-none md:shrink-0 md:text-right md:text-sm md:leading-[1.65] lg:whitespace-nowrap">
                <p>{{ __('landing_footer_legal_note') }}</p>
                <p>&copy; {{ now()->year }} {{ __('site_name') }}. {{ __('landing_footer_copyright_suffix') }}</p>
            </div>
        </div>
    </div>
</footer>
