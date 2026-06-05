@php
    use App\Support\LocalizedUrl;

    $cookiePolicyPath = LocalizedUrl::equivalentPath('/cookie-policy', app()->getLocale());
    $cookieConsentName = 'gyc_cookie_consent';
    $preferencesCookieName = 'gyc_cookie_preferences';
    $hasCookieConsent = filled(request()->cookie($cookieConsentName));
    $cookiePreferences = json_decode((string) request()->cookie($preferencesCookieName, ''), true);
    $cookiePreferences = is_array($cookiePreferences) ? $cookiePreferences : [];
    $sections = [
        'necessary' => [
            'title' => __('cookie_settings_necessary_title'),
            'summary' => __('cookie_settings_necessary_summary'),
            'details' => __('cookie_settings_necessary_details'),
            'checked' => true,
            'disabled' => true,
        ],
        'statistics' => [
            'title' => __('cookie_settings_statistics_title'),
            'summary' => __('cookie_settings_statistics_summary'),
            'details' => __('cookie_settings_statistics_details'),
            'checked' => (bool) ($cookiePreferences['statistics'] ?? false),
            'disabled' => false,
        ],
        'marketing' => [
            'title' => __('cookie_settings_marketing_title'),
            'summary' => __('cookie_settings_marketing_summary'),
            'details' => __('cookie_settings_marketing_details'),
            'checked' => (bool) ($cookiePreferences['marketing'] ?? false),
            'disabled' => false,
        ],
        'preferences' => [
            'title' => __('cookie_settings_preferences_title'),
            'summary' => __('cookie_settings_preferences_summary'),
            'details' => __('cookie_settings_preferences_details'),
            'checked' => (bool) ($cookiePreferences['preferences'] ?? false),
            'disabled' => false,
        ],
    ];
@endphp

@unless ($hasCookieConsent)
<div data-cookie-consent-modal class="fixed inset-0 z-70 flex items-center justify-center overflow-y-auto bg-brand-primary/40 px-4 py-6 backdrop-blur-[3px]">
    <div class="w-full max-w-3xl border border-slate-200 bg-white shadow-[0_32px_80px_rgba(52,48,106,0.22)]">
        <div class="border-b border-slate-200 px-5 py-5 sm:px-7">
            <p class="text-sm font-semibold text-brand-secondary">{{ __('cookie_banner_label') }}</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-brand-primary sm:text-[32px]">{{ __('cookie_banner_title') }}</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-[15px]">
                {{ __('cookie_banner_intro') }}
                <a href="{{ url($cookiePolicyPath) }}" class="font-semibold text-brand-primary underline decoration-brand-primary/35 underline-offset-3 transition-colors hover:text-brand-secondary">{{ __('cookie_policy') }}</a>
                {{ __('cookie_banner_outro') }}
            </p>
        </div>

        <div class="px-5 py-5 sm:px-7 sm:py-6">
            <div class="grid gap-3 sm:grid-cols-2">
                <button
                    type="button"
                    data-cookie-customize-toggle
                    aria-expanded="false"
                    class="inline-flex cursor-pointer items-center justify-center border border-brand-primary/16 px-4 py-3 text-sm font-semibold text-brand-primary transition-colors hover:border-brand-primary/28 hover:bg-brand-primary/3"
                >
                    {{ __('cookie_banner_customize') }}
                </button>

                <form method="POST" action="{{ route('cookie-consent.store') }}">
                    @csrf
                    <input type="hidden" name="consent" value="accepted">
                    <button
                        type="submit"
                        class="inline-flex w-full cursor-pointer items-center justify-center bg-brand-primary px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/90"
                    >
                        {{ __('cookie_banner_accept') }}
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('cookie-consent.store') }}" data-cookie-customize-panel class="mt-5 hidden border border-slate-200 bg-slate-50/70">
                @csrf
                <input type="hidden" name="consent" value="customized">
                <input type="hidden" name="preferences[necessary]" value="1">

                <div class="border-b border-slate-200 px-4 py-4">
                    <p class="text-sm font-semibold text-brand-primary">{{ __('cookie_banner_customize_title') }}</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ __('cookie_banner_customize_text') }}</p>
                </div>

                <div class="divide-y divide-slate-200">
                    @foreach ($sections as $key => $section)
                        <section data-cookie-accordion-item>
                            <div class="flex items-start justify-between gap-4 px-4 py-4">
                                <button
                                    type="button"
                                    data-cookie-accordion-trigger
                                    aria-expanded="false"
                                    class="flex min-w-0 flex-1 cursor-pointer items-start gap-3 text-left"
                                >
                                    <span data-cookie-chevron class="mt-1 shrink-0 text-brand-primary transition-transform">
                                        <x-marketing.icon name="chevron-right" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold text-brand-primary">{{ $section['title'] }}</span>
                                        <span class="mt-1 block text-sm leading-6 text-slate-600">{{ $section['summary'] }}</span>
                                    </span>
                                </button>

                                <label class="relative mt-0.5 inline-flex items-center {{ $section['disabled'] ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                    @unless ($section['disabled'])
                                        <input type="hidden" name="preferences[{{ $key }}]" value="0">
                                    @endunless
                                    <input
                                        type="checkbox"
                                        value="1"
                                        @if (!$section['disabled']) name="preferences[{{ $key }}]" @endif
                                        class="peer sr-only"
                                        @checked($section['checked'])
                                        @disabled($section['disabled'])
                                    >
                                    <span class="block h-6 w-11 border border-brand-primary/16 bg-slate-200 transition-colors peer-checked:bg-brand-primary peer-disabled:bg-brand-primary/20"></span>
                                    <span class="pointer-events-none absolute left-1 top-1 h-4 w-4 bg-white transition-transform peer-checked:translate-x-5"></span>
                                </label>
                            </div>

                            <div data-cookie-accordion-panel class="hidden border-t border-slate-200 px-4 py-4 text-sm leading-6 text-slate-600">
                                {{ $section['details'] }}
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="border-t border-slate-200 px-4 py-4">
                    <button
                        type="submit"
                        class="inline-flex w-full cursor-pointer items-center justify-center bg-brand-primary px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/90"
                    >
                        {{ __('cookie_banner_save_preferences') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        var modal = document.querySelector('[data-cookie-consent-modal]');

        if (!modal) {
            return;
        }

        var previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        modal.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                document.body.style.overflow = previousOverflow;
            });
        });

        var customizeToggle = modal.querySelector('[data-cookie-customize-toggle]');
        var customizePanel = modal.querySelector('[data-cookie-customize-panel]');

        if (customizeToggle && customizePanel) {
            customizeToggle.addEventListener('click', function () {
                var isExpanded = customizeToggle.getAttribute('aria-expanded') === 'true';
                customizeToggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
                customizePanel.classList.toggle('hidden', isExpanded);

                if (!isExpanded) {
                    var firstAccordion = customizePanel.querySelector('[data-cookie-accordion-trigger]');

                    if (firstAccordion) {
                        firstAccordion.focus();
                    }
                }
            });
        }

        modal.querySelectorAll('[data-cookie-accordion-trigger]').forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var panel = trigger.closest('[data-cookie-accordion-item]').querySelector('[data-cookie-accordion-panel]');
                var chevron = trigger.querySelector('[data-cookie-chevron]');
                var isExpanded = trigger.getAttribute('aria-expanded') === 'true';

                trigger.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
                panel.classList.toggle('hidden', isExpanded);

                if (chevron) {
                    chevron.classList.toggle('rotate-90', !isExpanded);
                }
            });
        });
    })();
</script>
@endunless