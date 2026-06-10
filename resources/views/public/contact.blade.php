<x-layouts.public-marketing
    :title="__('contact').' | Get Your Consultant'"
    :description="__('contact_page_desc')"
    :canonical="$canonical"
    :alternates="$alternates"
    :x-default="$xDefault"
>
    <section class="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]">
        <img src="{{ asset('images/blue-noise-texture.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.06] mix-blend-multiply">

        <div class="relative mx-auto max-w-6xl px-4 py-12 sm:px-6 md:py-16 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-[2.45rem] leading-[0.98] font-extrabold tracking-[-0.05em] text-brand-primary md:text-[3.5rem] md:leading-[0.95]">
                    {{ __('contact_page_title') }}
                </h1>
                <p class="mt-4 max-w-2xl text-[14px] leading-[1.7] text-brand-primary/78 md:text-base">
                    {{ __('contact_page_desc') }}
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1.08fr)_minmax(300px,0.72fr)] lg:gap-8">
                <div class="border solid-border solid-border-warm bg-white p-6 md:p-8 lg:p-9">
                    <h2 class="text-[1.55rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[1.9rem]">
                        {{ __('contact_form_card_title') }}
                    </h2>
                    <p class="mt-2 text-[14px] leading-[1.68] text-brand-primary/76">
                        {{ __('contact_form_required_note') }}
                    </p>

                    @if (session('success'))
                        <div class="mt-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form id="contact-form" action="{{ route('contact.store') }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        @if ($recaptchaSiteKey)
                            <input type="hidden" name="recaptcha_token" id="recaptcha-token">
                        @endif

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-medium text-brand-primary">
                                    {{ __('contact_form_name') }}
                                </label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    placeholder="{{ __('contact_form_name_placeholder') }}"
                                    autocomplete="name"
                                    required
                                    @class([
                                        'w-full border px-4 py-3 text-sm text-brand-primary shadow-sm outline-none transition-colors placeholder:text-brand-primary/45 md:text-base',
                                        'border-red-500 focus:border-red-500' => $errors->has('name'),
                                        'border-brand-primary/30 focus:border-brand-primary/60' => !$errors->has('name'),
                                    ])
                                >
                                @error('name')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-brand-primary">
                                    {{ __('contact_form_email') }}
                                </label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    placeholder="{{ __('contact_form_email_placeholder') }}"
                                    autocomplete="email"
                                    required
                                    @class([
                                        'w-full border px-4 py-3 text-sm text-brand-primary shadow-sm outline-none transition-colors placeholder:text-brand-primary/45 md:text-base',
                                        'border-red-500 focus:border-red-500' => $errors->has('email'),
                                        'border-brand-primary/30 focus:border-brand-primary/60' => !$errors->has('email'),
                                    ])
                                >
                                @error('email')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="mb-1.5 block text-sm font-medium text-brand-primary">
                                {{ __('contact_form_subject') }}
                            </label>
                            <input
                                id="subject"
                                name="subject"
                                type="text"
                                value="{{ old('subject') }}"
                                placeholder="{{ __('contact_form_subject_placeholder') }}"
                                required
                                @class([
                                    'w-full border px-4 py-3 text-sm text-brand-primary shadow-sm outline-none transition-colors placeholder:text-brand-primary/45 md:text-base',
                                    'border-red-500 focus:border-red-500' => $errors->has('subject'),
                                    'border-brand-primary/30 focus:border-brand-primary/60' => !$errors->has('subject'),
                                ])
                            >
                            @error('subject')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="mb-1.5 block text-sm font-medium text-brand-primary">
                                {{ __('contact_form_message') }}
                            </label>
                            <textarea
                                id="message"
                                name="message"
                                rows="7"
                                placeholder="{{ __('contact_form_message_placeholder') }}"
                                required
                                @class([
                                    'min-h-40 w-full border px-4 py-3 text-sm text-brand-primary shadow-sm outline-none transition-colors placeholder:text-brand-primary/45 md:text-base',
                                    'border-red-500 focus:border-red-500' => $errors->has('message'),
                                    'border-brand-primary/30 focus:border-brand-primary/60' => !$errors->has('message'),
                                ])
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @error('recaptcha_token')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92 md:text-base">
                                <x-marketing.icon name="envelope-simple" weight="bold" class="h-4 w-4" />
                                {{ __('contact_form_submit') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="space-y-4">
                    <div class="border solid-border solid-border-warm bg-white p-6">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]">
                                <x-marketing.icon name="map-pin-line" weight="fill" class="h-[22px] w-[22px] text-brand-tertiary" />
                            </div>
                            <div>
                                <h3 class="mb-1 text-[0.95rem] font-semibold text-brand-primary">
                                    {{ __('contact_sidebar_address_title') }}
                                </h3>
                                <p class="whitespace-pre-line text-[14px] leading-[1.68] text-brand-primary/78">{{ __('landing_footer_address') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center bg-white">
                                <x-marketing.icon name="clock-countdown" weight="fill" class="h-[22px] w-[22px] text-brand-secondary" />
                            </div>
                            <div>
                                <h3 class="mb-1 text-[0.95rem] font-semibold text-brand-primary">
                                    {{ __('contact_sidebar_response_title') }}
                                </h3>
                                <p class="text-[14px] leading-[1.68] text-brand-primary/78">
                                    {{ __('contact_sidebar_response_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($recaptchaSiteKey)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('contact-form');
                const tokenInput = document.getElementById('recaptcha-token');

                if (!form || !tokenInput || typeof grecaptcha === 'undefined') {
                    return;
                }

                form.addEventListener('submit', function (event) {
                    if (tokenInput.value) {
                        return;
                    }

                    event.preventDefault();

                    grecaptcha.ready(function () {
                        grecaptcha.execute(@json($recaptchaSiteKey), { action: 'contact_form' })
                            .then(function (token) {
                                tokenInput.value = token;
                                form.submit();
                            });
                    });
                });
            });
        </script>
    @endif
</x-layouts.public-marketing>
