@php
    $locale = app()->getLocale();

    $heroBenefits = [
        __('landing_hero_benefit_1'),
        __('landing_hero_benefit_2'),
        __('landing_hero_benefit_3'),
    ];

    $whyCards = [
        ['icon' => 'currency', 'title' => __('landing_why_item_1_title'), 'body' => __('landing_why_item_1_desc')],
        ['icon' => 'compare', 'title' => __('landing_why_item_2_title'), 'body' => __('landing_why_item_2_desc')],
        ['icon' => 'warning', 'title' => __('landing_why_item_3_title'), 'body' => __('landing_why_item_3_desc')],
        ['icon' => 'trend', 'title' => __('landing_why_item_4_title'), 'body' => __('landing_why_item_4_desc')],
    ];

    $exampleFeatures = [
        ['icon' => 'currency', 'label' => __('landing_example_feature_1')],
        ['icon' => 'compare', 'label' => __('landing_example_feature_2')],
        ['icon' => 'warning', 'label' => __('landing_example_feature_3')],
        ['icon' => 'scale', 'label' => __('landing_example_feature_4')],
        ['icon' => 'chart', 'label' => __('landing_example_feature_5')],
    ];

    $howCards = [
        ['number' => '1', 'icon' => 'link', 'title' => __('landing_process_item_1_title'), 'body' => __('landing_process_item_1_desc')],
        ['number' => '2', 'icon' => 'search', 'title' => __('landing_process_item_2_title'), 'body' => __('landing_process_item_2_desc')],
        ['number' => '3', 'icon' => 'file-pdf', 'title' => __('landing_process_item_3_title'), 'body' => __('landing_process_item_3_desc')],
    ];

    $pricingOptions = [
        [
            'type' => 'buying_living',
            'label' => __('buying'),
            'fallback_price' => __('landing_pricing_buying_price'),
            'image' => asset('images/v2/pricing-buying-apartment.png'),
            'image_alt' => __('landing_pricing_buying_visual_label'),
            'features' => [
                __('landing_pricing_buying_feature_1'),
                __('landing_pricing_buying_feature_2'),
                __('landing_pricing_buying_feature_3'),
                __('landing_pricing_buying_feature_4'),
            ],
        ],
        [
            'type' => 'rental_living',
            'label' => __('rental'),
            'fallback_price' => __('landing_pricing_rental_price'),
            'image' => asset('images/v2/pricing-rental-apartmen.png'),
            'image_alt' => __('landing_pricing_rental_visual_label'),
            'features' => [
                __('landing_pricing_rental_feature_1'),
                __('landing_pricing_rental_feature_2'),
                __('landing_pricing_rental_feature_3'),
                __('landing_pricing_rental_feature_4'),
            ],
        ],
    ];

    $buyingSampleReportHref = asset($locale === 'ro' ? 'images/report-example-ro.pdf' : 'images/report-example-en.pdf');
    $rentalSampleReportHref = asset($locale === 'ro' ? 'images/report-example-rental-ro.pdf' : 'images/report-example-rental-en.pdf');
    $testimonialSection = $locale === 'ro'
        ? [
            'eyebrow' => 'Review-uri reale',
            'title' => 'Ce spun cei care au folosit raportul',
            'desc' => 'Feedback primit de la oameni care au folosit GetYourConsultant înainte să ia o decizie imobiliară.',
            'previous' => 'Review-ul anterior',
            'next' => 'Review-ul următor',
            'items' => [
                ['text' => 'Raportul m-a ajutat să-mi dau seama că proprietatea era supraevaluată. Eram foarte aproape să plătesc cu aproape 20.000 € mai mult decât ar fi avut sens.', 'rating' => 5],
                ['text' => 'Mi s-a părut simplu, rapid și ușor de urmărit. Pentru mine, a meritat.', 'rating' => 5],
                ['text' => 'Am cumpărat 5 rapoarte până acum și m-au ajutat să filtrez mult mai bine opțiunile. La două proprietăți am renunțat imediat, pentru că raportul mi-a arătat riscuri la care nu mă gândisem.', 'rating' => 5],
                ['text' => 'Mi-a plăcut că este clar și direct. Nu am simțit presiune sau interese ascunse, ci o analiză rece, obiectivă, exact cum aveam nevoie.', 'rating' => 5],
                ['text' => 'Am descoperit multe lucruri la care sincer nu mă gândisem. Acum mă uit altfel la o proprietate și știu mai bine ce întrebări să pun.', 'rating' => 4],
                ['text' => 'De când am descoperit raportul, încă nu am cumpărat nimic, pentru că la fiecare proprietate găsesc câte ceva de verificat. 😅 Dar prefer să nu mă grăbesc și să iau o decizie informată.', 'rating' => 5],
            ],
        ]
        : [
            'eyebrow' => 'Real reviews',
            'title' => 'What customers say after using the report',
            'desc' => 'Feedback from people who used GetYourConsultant before making a real estate decision.',
            'previous' => 'Previous review',
            'next' => 'Next review',
            'items' => [
                ['text' => 'The report helped me realize the property was overvalued. I was very close to paying almost €20,000 more than made sense.', 'rating' => 5],
                ['text' => 'It felt simple, fast, and easy to follow. For me, it was worth it.', 'rating' => 5],
                ['text' => 'I have bought 5 reports so far, and they helped me filter my options much better. I dropped two properties right away because the report showed risks I had not considered.', 'rating' => 5],
                ['text' => 'I liked that it was clear and direct. I did not feel any pressure or hidden interests, just a cold, objective analysis, exactly what I needed.', 'rating' => 5],
                ['text' => 'I discovered a lot of things I honestly had not thought about. Now I look at properties differently and know better what questions to ask.', 'rating' => 4],
                ['text' => 'Since discovering the report, I still have not bought anything because I keep finding something to check in every property. 😅 But I would rather not rush and make an informed decision.', 'rating' => 5],
            ],
        ];
    $professionalSection = $locale === 'ro'
        ? [
            'eyebrow' => 'Opinii profesionale',
            'title' => 'Recomandări de la profesioniști',
            'desc' => 'Perspective de la specialiști care înțeleg cât de mult contează contextul, datele și verificarea atentă înainte de o decizie imobiliară.',
            'items' => [
                [
                    'featured' => true,
                    'body' => [
                        'GetYourConsultant a fost creat pentru a ajuta cumpărătorii și chiriașii să ia decizii mai informate. Raportul oferă o analiză independentă bazată pe date și indicatori de piață, însă nu înlocuiește opinia unui agent imobiliar, a unui evaluator autorizat sau a altui specialist. Scopul nostru este să oferim claritate, informații suplimentare și economisirea timpului, înainte de o decizie importantă.',
                    ],
                    'role' => 'Fondator & CEO, GetYourConsultant',
                    'name' => 'Cosmin M.',
                ],
                [
                    'body' => [
                        'Piața imobiliară din România este extraordinar de diversă — de la blocuri interbelice pline de farmec până la complexe rezidențiale noi sau penthouse-uri exclusiviste. Să evaluezi corect fiecare tipologie implică zeci de criterii pe care, în mod normal, este aproape imposibil să le aduni și să le interpretezi de unul singur.',
                        'Tocmai aici intervine valoarea unui astfel de raport GetYourConsultant. Beneficiul său nu constă doar în volumul informațiilor oferite, ci în modul în care acestea sunt corelate și puse în context. O proprietate nu ar trebui analizată exclusiv prin prisma prețului sau a aspectului său, ci prin raportarea simultană la contextul urban, riscurile tehnice, costurile viitoare și potențialul real de utilizare.',
                    ],
                    'role' => 'Arhitect',
                    'name' => 'Andrei P.',
                ],
                [
                    'body' => [
                        'În arhitectură, cele mai bune decizii sunt cele luate pe baza informațiilor corecte, nu doar a impresiilor de moment. Din acest motiv, consider că un astfel de raport reprezintă un instrument valoros pentru oricine dorește să înțeleagă cu adevărat proprietatea pe care urmează să o cumpere și să reducă riscurile unei decizii luate exclusiv pe criterii emoționale.',
                    ],
                    'role' => 'Arhitect',
                    'name' => 'Vlad M.',
                ],
                [
                    'body' => [
                        'O idee unică până acum. Lucrez de 13 ani în imobiliare și nu am întâlnit nimic asemănător. Site-ul oferă un raport foarte precis, util atât pentru investitori, cât și pentru clienți, care până acum erau nevoiți să se bazeze pe instinct sau pe opinia agentului imobiliar. Într-o economie aflată într-o continuă schimbare, acest instrument poate deveni un partener de încredere.',
                    ],
                    'role' => 'Dezvoltator imobiliar',
                    'name' => 'Costin Girba',
                ],
                [
                    'body' => [
                        'Fiecare tip de proprietate vine cu propriul set de riscuri, particularități tehnice și întrebări la care, de cele mai multe ori, cumpărătorul obișnuit nu știe nici măcar că ar trebui să le pună. Exact aceasta este valoarea unui raport de analiză bine structurat: nu trebuie să fii specialist ca să iei o decizie informată.',
                    ],
                    'role' => 'Arhitect și designer interior',
                    'name' => 'Andreea S.',
                ],
                [
                    'body' => [
                        'În calitate de avocat, apreciez în mod deosebit claritatea, structura și relevanța informațiilor prezentate în acest raport. Documentul GetYourConsultant oferă o imagine completă asupra imobilului analizat și contribuie semnificativ la eficientizarea procesului de verificare și evaluare. Îl consider un instrument util pentru orice persoană care dorește să ia decizii informate în domeniul imobiliar.',
                    ],
                    'role' => 'Avocat',
                    'name' => 'Florin Roșu',
                ],
                [
                    'body' => [
                        'Din perspectiva pieței, cred că astfel de soluții vor deveni tot mai comune, iar cei care vor folosi astfel de instrumente vor avea un avantaj în procesul de tranzacționare.',
                    ],
                    'role' => 'Agent imobiliar',
                    'name' => 'Daniel M.',
                ],
                [
                    'body' => [
                        'Ca agent imobiliar, cred că un cumpărător informat ia întotdeauna decizii mai bune. GetYourConsultant oferă o perspectivă utilă asupra pieței și poate ajuta la evaluarea unei proprietăți înainte de achiziție.',
                    ],
                    'role' => 'Agent imobiliar',
                    'name' => 'Andrei Pavel',
                ],
                [
                    'body' => [
                        'În imobiliare, informația contează. Mi se pare util să existe instrumente care ajută cumpărătorii să analizeze mai bine o proprietate înainte de a lua o decizie.',
                    ],
                    'role' => 'Agent imobiliar',
                    'name' => 'Oana V.',
                ],
            ],
        ]
        : [
            'eyebrow' => 'Professional opinions',
            'title' => 'Recommendations from professionals',
            'desc' => 'Perspectives from specialists who understand how much context, data, and careful verification matter before a real estate decision.',
            'items' => [
                [
                    'featured' => true,
                    'body' => [
                        'GetYourConsultant was created to help buyers and tenants make more informed decisions. The report provides an independent analysis based on data and market indicators, but it does not replace the opinion of a real estate agent, an authorized appraiser, or another specialist. Our goal is to provide clarity, additional information, and time savings before an important decision.',
                    ],
                    'role' => 'Founder & CEO, GetYourConsultant',
                    'name' => 'Cosmin M.',
                ],
                [
                    'body' => [
                        'Romania’s real estate market is extraordinarily diverse, from charming interwar buildings to new residential complexes or exclusive penthouses. Correctly evaluating each typology involves dozens of criteria that are almost impossible to gather and interpret on your own.',
                        'This is exactly where a GetYourConsultant report adds value. Its benefit is not only the amount of information provided, but the way that information is correlated and placed in context. A property should not be analyzed only through price or appearance, but also through urban context, technical risks, future costs, and real usability potential.',
                    ],
                    'role' => 'Architect',
                    'name' => 'Andrei P.',
                ],
                [
                    'body' => [
                        'In architecture, the best decisions are made based on correct information, not just momentary impressions. For this reason, I consider this type of report a valuable tool for anyone who wants to truly understand the property they are about to buy and reduce the risks of a decision made exclusively on emotional criteria.',
                    ],
                    'role' => 'Architect',
                    'name' => 'Vlad M.',
                ],
                [
                    'body' => [
                        'A unique idea so far. I have worked in real estate for 13 years and have not encountered anything similar. The site provides a very precise report, useful both for investors and for clients, who until now had to rely on instinct or on the opinion of the real estate agent. In an economy that is constantly changing, this tool can become a trusted partner.',
                    ],
                    'role' => 'Real estate developer',
                    'name' => 'Costin Girba',
                ],
                [
                    'body' => [
                        'Every type of property comes with its own risks, technical particularities, and questions that the average buyer often does not even know they should ask. This is exactly the value of a well-structured analysis report: you do not need to be a specialist to make an informed decision.',
                    ],
                    'role' => 'Architect and interior designer',
                    'name' => 'Andreea S.',
                ],
                [
                    'body' => [
                        'As a lawyer, I particularly appreciate the clarity, structure, and relevance of the information presented in this report. The GetYourConsultant document provides a complete view of the analyzed property and significantly streamlines the verification and evaluation process. I consider it a useful tool for anyone who wants to make informed decisions in real estate.',
                    ],
                    'role' => 'Lawyer',
                    'name' => 'Florin Roșu',
                ],
                [
                    'body' => [
                        'From a market perspective, I believe these solutions will become increasingly common, and those who use such tools will have an advantage in the transaction process.',
                    ],
                    'role' => 'Real estate agent',
                    'name' => 'Daniel M.',
                ],
                [
                    'body' => [
                        'As a real estate agent, I believe an informed buyer always makes better decisions. GetYourConsultant offers a useful perspective on the market and can help evaluate a property before purchase.',
                    ],
                    'role' => 'Real estate agent',
                    'name' => 'Andrei Pavel',
                ],
                [
                    'body' => [
                        'In real estate, information matters. I find it useful to have tools that help buyers analyze a property better before making a decision.',
                    ],
                    'role' => 'Real estate agent',
                    'name' => 'Oana V.',
                ],
            ],
        ];
    $formatPrice = static function (array $entry, string $fallback) use ($locale): string {
        if ($entry === []) {
            return $fallback;
        }

        $amount = ((int) ($entry['base_amount_minor'] ?? 0)) / 100;
        $currency = strtoupper((string) ($entry['base_currency'] ?? 'EUR'));

        return ($locale === 'ro'
            ? number_format($amount, 2, ',', '.')
            : number_format($amount, 2, '.', ','))
            .' '
            .$currency;
    };
@endphp

<x-layouts.public-marketing
    :title="__('landing_meta_title')"
    :description="__('landing_hero_desc')"
    :canonical="$canonical"
    :alternates="$alternates"
    :x-default="$xDefault"
>
    <section id="hero" class="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]">
                <img src="{{ asset('images/blue-noise-texture.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.07] mix-blend-multiply">

                <div class="relative mx-auto grid max-w-6xl gap-12 px-4 py-14 sm:px-6 md:py-18 lg:grid-cols-[minmax(0,0.98fr)_minmax(360px,0.92fr)] lg:items-center lg:px-8">
                    <div class="max-w-2xl">
                        <h1 class="text-[2.45rem] leading-[0.98] font-extrabold tracking-[-0.05em] text-brand-primary md:text-[3.7rem] md:leading-[0.95]">
                            {{ __('landing_hero_title_1') }}
                        </h1>
                        <p class="mt-4 max-w-xl text-[14px] leading-[1.6] text-brand-primary/82 md:mt-5 md:text-[1.05rem] md:leading-[1.72]">
                            {{ __('landing_hero_desc') }}
                        </p>

                        <div class="mt-7 max-w-xl md:mt-8">
                            <a href="{{ route('get-report') }}" class="inline-flex h-14 cursor-pointer items-center justify-center bg-brand-primary px-7 text-[0.98rem] font-semibold text-white shadow-[0_18px_36px_rgba(52,48,106,0.22)] transition-colors hover:bg-brand-primary/92">
                                <span class="inline-flex items-center gap-2">
                                    {{ __('landing_generate_report') }}
                                    <x-marketing.icon name="arrow-right" class="h-4 w-4" />
                                </span>
                            </a>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 md:mt-6 md:gap-x-6">
                            @foreach ($heroBenefits as $benefit)
                                <div class="flex items-center gap-2 text-[14px] font-semibold text-brand-primary md:text-[1.05rem]">
                                    <x-marketing.icon name="check-circle" weight="fill" class="h-[18px] w-[18px] shrink-0 text-brand-secondary" />
                                    <span class="leading-[1.35]">{{ $benefit }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-7 max-w-xl">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                                <p class="text-[14px] font-bold text-brand-primary">{{ __('landing_social_proof_summary') }}</p>
                                <div class="flex items-center gap-1 text-[#f3b44f]">
                                    @for ($star = 0; $star < 5; $star++)
                                        <x-marketing.icon name="star" weight="fill" class="h-4 w-4" />
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-2 max-w-[32rem] text-[14px] leading-[1.6] text-brand-primary/76">
                                {{ __('landing_social_proof_note') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="relative flex min-h-[332px] items-center justify-center py-1 md:min-h-[620px] md:p-2">
                            <div class="absolute inset-x-[10%] bottom-10 h-20 rounded-full bg-brand-primary/14 blur-3xl"></div>
                            <div class="relative flex min-h-[300px] w-full items-center justify-center md:min-h-[580px]">
                                <img src="{{ asset('images/v2/hero-property-analysis.png') }}" alt="{{ __('landing_hero_visual_title') }}" class="relative z-10 max-h-[332px] w-full scale-[1.2] object-contain drop-shadow-[0_24px_40px_rgba(52,48,106,0.16)] md:max-h-[620px] md:scale-[1.3] md:drop-shadow-[0_34px_56px_rgba(52,48,106,0.22)]">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="why-check" class="border-b border-brand-primary/85 bg-brand-primary py-16 md:py-18">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl text-center">
                        <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-white md:text-[2.8rem]">{{ __('landing_why_title') }}</h2>
                        <p class="mt-3 text-[14px] leading-[1.6] text-white/78 md:mt-4 md:text-lg md:leading-[1.72]">{{ __('landing_why_desc') }}</p>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-3 md:mt-10 md:gap-4 xl:grid-cols-4">
                        @foreach ($whyCards as $card)
                            <div class="bg-transparent px-3 py-4 text-center md:px-6 md:py-8">
                                <div class="flex items-center justify-center text-white">
                                    <x-marketing.icon :name="$card['icon']" weight="bold" class="h-9 w-9 md:h-14 md:w-14" />
                                </div>
                                <h3 class="mt-3 text-[0.95rem] font-semibold leading-[1.18] text-white md:mt-6 md:text-xl">{{ $card['title'] }}</h3>
                                <p class="mt-1.5 text-[12px] leading-[1.45] text-white/78 md:mt-3 md:text-[1rem] md:leading-[1.68]">{{ $card['body'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="report-example" class="relative overflow-hidden bg-white pb-16 pt-12 md:py-18">
                <img src="{{ asset('images/blue-noise-texture.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.045] mix-blend-multiply">

                <div class="relative mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-[minmax(340px,0.94fr)_minmax(0,1.06fr)] lg:items-center lg:px-8">
                    <div>
                        <div class="relative flex min-h-[332px] items-center justify-center py-1 md:min-h-[620px] md:p-2">
                            <div class="absolute inset-x-[16%] bottom-12 h-18 rounded-full bg-brand-primary/12 blur-3xl"></div>
                            <img src="{{ asset('images/v2/report-first-page-illustration.png') }}" alt="{{ __('landing_example_visual_title') }}" class="relative z-10 max-h-[332px] w-full object-contain drop-shadow-[0_22px_34px_rgba(52,48,106,0.14)] md:max-h-[620px] md:drop-shadow-[0_32px_48px_rgba(52,48,106,0.2)]">
                        </div>
                    </div>

                    <div class="max-w-2xl">
                        <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">{{ __('landing_example_title') }}</h2>
                        <p class="mt-3 text-[14px] leading-[1.6] text-brand-primary/78 md:mt-4 md:text-lg md:leading-[1.76]">{{ __('landing_example_desc') }}</p>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2 md:mt-6">
                            @foreach ($exampleFeatures as $feature)
                                <div class="flex items-center gap-3 border border-brand-primary/10 bg-white px-4 py-4 shadow-[0_10px_24px_rgba(52,48,106,0.06)]">
                                    <x-marketing.icon :name="$feature['icon']" weight="bold" class="h-5 w-5 text-brand-secondary" />
                                    <span class="text-[14px] leading-[1.45] text-brand-primary md:text-[1rem]">{{ $feature['label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            <a href="{{ $buyingSampleReportHref }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center gap-2 bg-brand-primary px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_34px_rgba(52,48,106,0.18)] transition-colors hover:bg-brand-primary/92">
                                <x-marketing.icon name="file-pdf" class="h-4 w-4" />
                                {{ __('landing_example_buying_cta') }}
                                <x-marketing.icon name="arrow-right" class="h-4 w-4" />
                            </a>
                            <a href="{{ $rentalSampleReportHref }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center gap-2 border border-brand-primary/12 bg-[linear-gradient(180deg,#eef2ff_0%,#dde7ff_100%)] px-6 py-3 text-sm font-semibold text-brand-primary shadow-[0_18px_34px_rgba(52,48,106,0.1)] transition-colors hover:border-brand-primary/20 hover:bg-[linear-gradient(180deg,#e7eeff_0%,#d3e0ff_100%)]">
                                <x-marketing.icon name="file-pdf" class="h-4 w-4" />
                                {{ __('landing_example_rental_cta') }}
                                <x-marketing.icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="how-it-works" class="border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f8faff_100%)] py-16 md:pt-0 md:pb-16">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl text-center">
                        <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">{{ __('landing_how_title') }}</h2>
                        <p class="mt-3 text-[14px] leading-[1.6] text-brand-primary/74 md:mt-4 md:text-lg md:leading-[1.72]">{{ __('landing_how_desc') }}</p>
                    </div>

                    <div class="mt-8 grid gap-5 lg:mt-10 lg:grid-cols-3 lg:gap-8">
                        @foreach ($howCards as $index => $card)
                            <div class="relative">
                                @if ($index < count($howCards) - 1)
                                    <div class="pointer-events-none absolute right-[-26px] top-[82px] hidden h-12 w-12 items-center justify-center rounded-full border border-brand-primary/10 bg-white text-brand-primary shadow-[0_12px_28px_rgba(52,48,106,0.08)] lg:flex">
                                        <x-marketing.icon name="arrow-right" weight="bold" class="h-5 w-5" />
                                    </div>
                                @endif

                                <div class="h-full border border-brand-primary/10 bg-white px-6 py-7 shadow-[0_18px_42px_rgba(52,48,106,0.08)]">
                                    <div class="relative flex h-22 w-22 items-center justify-center rounded-full bg-[#f3f6ff] text-brand-primary">
                                        <span class="absolute left-0 top-0 flex h-8 w-8 -translate-x-1/4 -translate-y-1/4 items-center justify-center rounded-full bg-brand-primary text-sm font-semibold text-white shadow-[0_10px_22px_rgba(52,48,106,0.24)]">{{ $card['number'] }}</span>
                                        <x-marketing.icon :name="$card['icon']" weight="bold" class="h-10 w-10" />
                                    </div>
                                    <h3 class="mt-5 text-lg font-semibold text-brand-primary md:mt-6 md:text-xl">{{ $card['title'] }}</h3>
                                    <p class="mt-2 text-[14px] leading-[1.6] text-brand-primary/76 md:mt-3 md:text-[1rem] md:leading-[1.7]">{{ $card['body'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="reviews" class=" bg-white py-10 md:py-12">
                <div class="mx-auto grid max-w-6xl gap-6 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start lg:px-8">
                    <div class="max-w-3xl">
                        <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.4rem]">{{ __('landing_reviews_title') }}</h2>
                        <p class="mt-3 max-w-2xl text-[14px] leading-[1.6] text-brand-primary/76 md:text-base md:leading-[1.7]">{{ __('landing_reviews_desc') }}</p>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row lg:justify-end" aria-label="review-stats">
                        <div class="border border-brand-primary/10 bg-[linear-gradient(180deg,#ffffff_0%,#eef2ff_100%)] px-5 py-5 shadow-[0_14px_34px_rgba(52,48,106,0.08)] sm:w-[320px]">
                            <div class="flex flex-col gap-3">
                                <p class="text-[1.7rem] font-bold leading-[0.95] tracking-[-0.05em] text-brand-primary md:text-[2.2rem] md:tracking-[-0.06em]">{{ __('landing_social_proof_summary') }}</p>
                                <div class="flex items-center gap-1 text-[#f3b44f]">
                                    @for ($star = 0; $star < 5; $star++)
                                        <x-marketing.icon name="star" weight="fill" class="h-4 w-4" />
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-3 max-w-[16rem] text-[14px] leading-[1.6] text-brand-primary/74 md:text-base md:leading-[1.7]">{{ __('landing_reviews_stat_reports_body') }}</p>
                        </div>

                        <div class="border border-brand-primary/10 bg-brand-primary px-5 py-5 text-white shadow-[0_18px_44px_rgba(52,48,106,0.16)] sm:w-[320px]">
                            <div class="flex items-end gap-3">
                                <p class="text-[1.7rem] font-bold leading-none tracking-[-0.05em] md:text-[2.2rem] md:tracking-[-0.06em]">4.9/5</p>
                                <div class="mb-1 flex items-center gap-1 text-[#f3b44f]">
                                    @for ($star = 0; $star < 5; $star++)
                                        <x-marketing.icon name="star" weight="fill" class="h-4 w-4" />
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-3 text-[14px] leading-[1.6] text-white/76 md:text-base md:leading-[1.7]">{{ __('landing_reviews_stat_rating_body') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="professional-recommendations" class="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#fff7ed_0%,#fffaf5_100%)] py-16 md:py-18">
                <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-[#f3b44f]/18 blur-3xl"></div>
                <div class="pointer-events-none absolute -left-24 bottom-0 h-64 w-64 rounded-full bg-brand-secondary/8 blur-3xl"></div>

                <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-3xl">
                            <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">{{ $professionalSection['title'] }}</h2>
                            <p class="mt-3 max-w-2xl text-[14px] leading-[1.6] text-brand-primary/76 md:mt-4 md:text-lg md:leading-[1.74]">{{ $professionalSection['desc'] }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" data-professionals-prev aria-label="Previous recommendation" class="flex h-11 w-11 cursor-pointer items-center justify-center border border-[#f3b44f]/30 bg-white text-lg font-semibold text-brand-primary shadow-[0_14px_34px_rgba(52,48,106,0.08)] transition-colors hover:border-[#f3b44f]/55 hover:bg-white/78 disabled:cursor-not-allowed disabled:opacity-40">&lt;</button>
                            <button type="button" data-professionals-next aria-label="Next recommendation" class="flex h-11 w-11 cursor-pointer items-center justify-center border border-[#f3b44f]/30 bg-white text-lg font-semibold text-brand-primary shadow-[0_14px_34px_rgba(52,48,106,0.08)] transition-colors hover:border-[#f3b44f]/55 hover:bg-white/78 disabled:cursor-not-allowed disabled:opacity-40">&gt;</button>
                        </div>
                    </div>

                    <div data-professionals-carousel class="-mx-4 mt-8 flex snap-x snap-mandatory items-start gap-6 overflow-x-auto px-4 pb-4 [-ms-overflow-style:none] [scrollbar-width:none] md:mt-10 [&::-webkit-scrollbar]:hidden">
                        @foreach ($professionalSection['items'] as $recommendation)
                            @php
                                $isFeaturedRecommendation = (bool) ($recommendation['featured'] ?? false);
                                $isCollapsibleRecommendation = $loop->iteration === 2;
                            @endphp
                            <article data-professional-card class="flex min-w-[86%] snap-start flex-col justify-between border border-[#f3b44f]/24 bg-white/92 p-6 text-brand-primary shadow-[0_8px_20px_rgba(52,48,106,0.06)] backdrop-blur-sm sm:min-w-[500px] lg:min-w-[520px] md:p-7">
                                <div>
                                    <div class="mb-5 h-1 w-16 bg-[#f3b44f]"></div>
                                    @if ($isCollapsibleRecommendation)
                                        <div data-expandable-recommendation>
                                            <p class="text-[15px] font-medium leading-[1.72] text-brand-primary/82 md:text-base md:leading-[1.78]">
                                                “{{ $recommendation['body'][0] }}”
                                            </p>
                                            <div data-expandable-recommendation-content class="hidden">
                                                @foreach (array_slice($recommendation['body'], 1) as $paragraph)
                                                    <p class="mt-4 text-[15px] font-medium leading-[1.72] text-brand-primary/82 md:text-base md:leading-[1.78]">
                                                        “{{ $paragraph }}”
                                                    </p>
                                                @endforeach
                                            </div>
                                            @if (count($recommendation['body']) > 1)
                                                <button type="button" data-expandable-recommendation-toggle aria-expanded="false" class="mt-4 inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-[#b86f12] transition-colors hover:text-brand-primary">
                                                    <span data-expandable-recommendation-label>Mai mult</span>
                                                    <span data-expandable-recommendation-icon class="text-lg leading-none transition-transform">↓</span>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        @foreach ($recommendation['body'] as $paragraph)
                                            <p class="text-[15px] font-medium leading-[1.72] text-brand-primary/82 md:text-base md:leading-[1.78] {{ $loop->first ? '' : 'mt-4' }}">
                                                “{{ $paragraph }}”
                                            </p>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="mt-6 border-t border-[#f3b44f]/24 pt-4">
                                    <p class="text-sm font-semibold text-[#b86f12]">{{ $recommendation['role'] }}</p>
                                    <p class="mt-1 text-lg font-bold tracking-[-0.03em] text-brand-primary">{{ $recommendation['name'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="customer-reviews" class="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] py-16 md:py-18">
                <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-3xl">
                            <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">{{ $testimonialSection['title'] }}</h2>
                            <p class="mt-3 max-w-2xl text-[14px] leading-[1.6] text-brand-primary/76 md:mt-4 md:text-lg md:leading-[1.74]">{{ $testimonialSection['desc'] }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" data-reviews-prev aria-label="{{ $testimonialSection['previous'] }}" class="flex h-11 w-11 cursor-pointer items-center justify-center border border-brand-primary/15 bg-white text-lg font-semibold text-brand-primary shadow-[0_14px_34px_rgba(52,48,106,0.08)] transition-colors hover:border-brand-primary/30 hover:bg-brand-primary/4 disabled:cursor-not-allowed disabled:opacity-40">&lt;</button>
                            <button type="button" data-reviews-next aria-label="{{ $testimonialSection['next'] }}" class="flex h-11 w-11 cursor-pointer items-center justify-center border border-brand-primary/15 bg-white text-lg font-semibold text-brand-primary shadow-[0_14px_34px_rgba(52,48,106,0.08)] transition-colors hover:border-brand-primary/30 hover:bg-brand-primary/4 disabled:cursor-not-allowed disabled:opacity-40">&gt;</button>
                        </div>
                    </div>

                    <div data-reviews-carousel class="-mx-4 mt-8 flex snap-x snap-mandatory gap-6 overflow-x-auto px-4 pb-4 [-ms-overflow-style:none] [scrollbar-width:none] md:mt-10 [&::-webkit-scrollbar]:hidden">
                        @foreach ($testimonialSection['items'] as $review)
                            @php
                                $reviewText = is_array($review) ? $review['text'] : $review;
                                $reviewRating = is_array($review) ? (int) ($review['rating'] ?? 5) : 5;
                            @endphp
                            <article data-review-card class="flex min-w-[84%] snap-start flex-col justify-between border border-brand-primary/10 bg-white p-6 shadow-[0_8px_20px_rgba(52,48,106,0.06)] sm:min-w-[420px] lg:min-w-[360px]">
                                <div>
                                    <div class="mb-5 flex items-center gap-1" aria-label="{{ $reviewRating }}/5">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <x-marketing.icon name="star" weight="fill" class="h-4 w-4 {{ $star <= $reviewRating ? 'text-[#f3b44f]' : 'text-brand-primary/16' }}" />
                                        @endfor
                                    </div>
                                    <p class="text-[16px] font-semibold leading-[1.62] tracking-[-0.015em] text-brand-primary md:text-[17px]">
                                        “{{ $reviewText }}”
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="pricing" class="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] py-16 md:py-18">
                <img src="{{ asset('images/blue-noise-texture.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.06] mix-blend-multiply">

                <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl text-center">
                        <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">{{ __('landing_pricing_title') }}</h2>
                        <p class="mt-3 text-[14px] leading-[1.6] text-brand-primary/76 md:mt-4 md:text-lg md:leading-[1.74]">{{ __('landing_pricing_desc') }}</p>
                    </div>

                    <div class="mt-10 flex flex-col items-center gap-5 lg:flex-row lg:justify-center">
                        @foreach ($pricingOptions as $option)
                            <div class="w-full max-w-[420px] overflow-hidden border border-brand-primary/10 bg-white text-brand-primary shadow-[0_22px_54px_rgba(52,48,106,0.08)]">
                                <div class="relative flex h-72 items-center justify-center overflow-hidden border-b border-brand-primary/10 bg-white p-6 md:h-84 md:p-8">
                                    <div class="absolute inset-x-10 bottom-5 h-8 rounded-full bg-brand-primary/6 blur-xl"></div>
                                    <img src="{{ $option['image'] }}" alt="{{ $option['image_alt'] }}" class="relative z-10 h-full w-full scale-[1.16] object-contain drop-shadow-[0_10px_18px_rgba(52,48,106,0.08)]">
                                </div>

                                <div class="px-6 py-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="text-xl font-semibold text-brand-primary">{{ $option['label'] }}</h3>
                                        <div class="text-right">
                                            <p class="text-3xl font-bold tracking-[-0.05em] text-brand-primary">
                                                {{ $formatPrice($pricingCatalog[$option['type']] ?? [], $option['fallback_price']) }}
                                            </p>
                                            <p class="mt-1 text-xs font-semibold text-brand-primary/54">{{ __('landing_price_vat_included') }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 space-y-2">
                                        @foreach ($option['features'] as $feature)
                                            <div class="flex items-start gap-3">
                                                <x-marketing.icon name="check-circle" weight="fill" class="mt-0.5 h-[18px] w-[18px] text-brand-secondary" />
                                                <span class="text-[14px] leading-[1.6] text-brand-primary/76 md:text-sm">{{ $feature }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <a href="{{ route('get-report', ['type' => $option['type']]) }}" class="mt-7 inline-flex items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92">
                                        {{ __('landing_pricing_cta') }}
                                        <x-marketing.icon name="arrow-right" class="h-4 w-4" />
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="final-cta" class="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#eaf1ff_0%,#f5f8ff_100%)] py-16 md:py-8">
                <img src="{{ asset('images/blue-noise-texture.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.07] mix-blend-multiply">

                <div class="relative mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-[minmax(0,0.98fr)_minmax(320px,0.9fr)] lg:items-center lg:px-8">
                    <div class="max-w-2xl">
                        <h2 class="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.8rem]">{{ __('landing_cta_title') }}</h2>
                        <p class="mt-3 text-[14px] leading-[1.6] text-brand-primary/78 md:mt-4 md:text-lg md:leading-[1.74]">{{ __('landing_cta_desc') }}</p>

                        <div class="mt-7 max-w-xl md:mt-8">
                            <form id="final-cta-form" action="{{ route('get-report') }}" method="GET" class="w-full">
                                <div class="flex flex-col gap-0 sm:flex-row sm:items-stretch">
                                    <input
                                        id="final-cta-form-url"
                                        name="url"
                                        type="url"
                                        placeholder="{{ __('landing_hero_url_placeholder') }}"
                                        class="h-14 border border-brand-primary/12 bg-[#fff] px-5 text-base text-brand-primary shadow-[0_16px_40px_rgba(52,48,106,0.08)] placeholder:text-brand-primary/45 focus:outline-none"
                                    >
                                    <button type="submit" class="h-14 cursor-pointer bg-brand-primary px-7 text-[0.98rem] font-semibold text-white shadow-[0_18px_36px_rgba(52,48,106,0.22)] transition-colors hover:bg-brand-primary/92 sm:-ml-px">
                                        <span class="inline-flex items-center gap-2">
                                            {{ __('landing_generate_report') }}
                                            <x-marketing.icon name="arrow-right" class="h-4 w-4" />
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-5 flex items-start gap-3 text-[14px] font-medium leading-[1.6] text-brand-primary/74 md:text-sm">
                            <x-marketing.icon name="check-circle" weight="fill" class="mt-0.5 h-6 w-6 shrink-0 text-brand-secondary" />
                            {{ __('landing_cta_support_note') }}
                        </div>
                    </div>

                    <div>
                        <div class="relative flex min-h-[332px] items-end justify-center py-1 md:min-h-[460px] md:p-4">
                            <div class="absolute inset-x-[14%] bottom-10 h-18 rounded-full bg-brand-primary/12 blur-3xl"></div>
                            <div class="relative flex min-h-[300px] w-full items-end justify-center md:min-h-[420px]">
                                <img src="{{ asset('images/v2/cta-apartment-cutout.png') }}" alt="{{ __('landing_cta_visual_title') }}" class="relative z-10 max-h-[332px] w-full object-contain drop-shadow-[0_20px_28px_rgba(52,48,106,0.12)] md:max-h-[450px] md:scale-[1.04] md:drop-shadow-[0_30px_44px_rgba(52,48,106,0.18)]">
                            </div>
                        </div>
                    </div>
                </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const setupCarousel = (carouselSelector, cardSelector, previousSelector, nextSelector) => {
                const carousel = document.querySelector(carouselSelector);
                const previousButton = document.querySelector(previousSelector);
                const nextButton = document.querySelector(nextSelector);

                if (!carousel || !previousButton || !nextButton) {
                    return;
                }

                const getStep = () => {
                    const card = carousel.querySelector(cardSelector);

                    if (!card) {
                        return carousel.clientWidth;
                    }

                    const style = window.getComputedStyle(carousel);
                    const gap = parseFloat(style.columnGap || style.gap || '0');

                    return card.getBoundingClientRect().width + gap;
                };

                const updateButtons = () => {
                    const maxScroll = carousel.scrollWidth - carousel.clientWidth - 4;

                    previousButton.disabled = carousel.scrollLeft <= 4;
                    nextButton.disabled = carousel.scrollLeft >= maxScroll;
                };

                previousButton.addEventListener('click', () => {
                    carousel.scrollBy({ left: -getStep(), behavior: 'smooth' });
                });

                nextButton.addEventListener('click', () => {
                    carousel.scrollBy({ left: getStep(), behavior: 'smooth' });
                });

                carousel.addEventListener('scroll', updateButtons, { passive: true });
                window.addEventListener('resize', updateButtons);
                updateButtons();
            };

            setupCarousel('[data-professionals-carousel]', '[data-professional-card]', '[data-professionals-prev]', '[data-professionals-next]');
            setupCarousel('[data-reviews-carousel]', '[data-review-card]', '[data-reviews-prev]', '[data-reviews-next]');

            document.querySelectorAll('[data-expandable-recommendation]').forEach((container) => {
                const toggle = container.querySelector('[data-expandable-recommendation-toggle]');
                const content = container.querySelector('[data-expandable-recommendation-content]');
                const label = container.querySelector('[data-expandable-recommendation-label]');
                const icon = container.querySelector('[data-expandable-recommendation-icon]');

                if (!toggle || !content) {
                    return;
                }

                toggle.addEventListener('click', () => {
                    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

                    toggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
                    content.classList.toggle('hidden', isExpanded);

                    if (label) {
                        label.textContent = isExpanded ? 'Mai mult' : 'Mai puțin';
                    }

                    if (icon) {
                        icon.classList.toggle('rotate-180', !isExpanded);
                    }
                });
            });
        });
    </script>
</x-layouts.public-marketing>
