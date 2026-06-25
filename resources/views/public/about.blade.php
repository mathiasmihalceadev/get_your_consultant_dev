<x-layouts.public-marketing
    title="Despre noi"
    description="Află cine este GetYourConsultant, ce oferă concret și cum ajutăm cumpărătorii, vânzătorii și chiriașii să ia decizii imobiliare mai informate."
    :canonical="$canonical"
    :alternates="$alternates"
    :x-default="$xDefault"
>
    <section class="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] py-8 md:py-20">
        <img src="{{ asset('images/blue-noise-texture.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.06] mix-blend-multiply">

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-[2.45rem] font-extrabold leading-[0.98] tracking-[-0.05em] text-brand-primary md:text-[3.7rem] md:leading-[0.95]">
                    Cine suntem?
                </h1>
                <p class="mt-5 text-[15px] leading-[1.72] text-brand-primary/78 md:text-lg md:leading-[1.78]">
                    Suntem o companie specializată în analiza și consultanța imobiliară, dedicată furnizării de informații clare și profesioniste pentru decizii imobiliare mai sigure.
                </p>
            </div>
        </div>
    </section>

    <section class="border-b solid-divider bg-white py-14 md:py-18">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <p class="text-[15px] leading-[1.78] text-brand-primary/78 md:text-base md:leading-[1.82]">
                    În prezent suntem activi în România, iar în paralel dezvoltăm structura companiei și la nivel european (Headquarters Estonia), cu obiectivul de a transforma GetYourConsultant într-un reper de încredere pentru analiza imobiliară în întreaga Europă.
                </p>

                <div class="border border-brand-primary/10 bg-[linear-gradient(180deg,#ffffff_0%,#f8faff_100%)] p-6 shadow-[0_14px_34px_rgba(52,48,106,0.06)] md:p-8">
                    <h2 class="text-[1.8rem] font-bold leading-[1.06] tracking-[-0.04em] text-brand-primary md:text-[2.4rem]">
                        Ce oferim concret?
                    </h2>
                    <div class="mt-5 space-y-5 text-[15px] leading-[1.78] text-brand-primary/78 md:text-base md:leading-[1.82]">
                        <p>
                            GetYourConsultant generează rapoarte imobiliare detaliate și profesioniste, concepute pentru a vă ajuta să luați decizii rapide, sigure și profitabile atunci când doriți să cumpărați, să vindeți sau să închiriați o proprietate.
                        </p>
                        <p>
                            Analizăm în profunzime piața, comparăm proprietatea cu oferte similare și vă oferim informații esențiale despre valoarea reală, poziționarea și potențialul acesteia. Astfel, economisiți timp, evitați deciziile bazate pe presupuneri și aveți acces la date clare care vă oferă un avantaj real în negociere.
                        </p>
                    </div>
                </div>

                <p class="text-[15px] leading-[1.78] text-brand-primary/78 md:text-base md:leading-[1.82]">
                    Independența și obiectivitatea sunt principii esențiale pentru noi. Nu modificăm și nu influențăm concluziile rapoartelor pentru a favoriza cumpărători, vânzători, agenți imobiliari sau alte părți implicate. Analizele noastre sunt realizate pe baza datelor disponibile și a criteriilor de evaluare utilizate în procesul de analiză, oferind o perspectivă cât mai obiectivă și transparentă asupra proprietății.
                </p>

                <p class="text-[15px] leading-[1.78] text-brand-primary/78 md:text-base md:leading-[1.82]">
                    Credem cu tărie că accesul la informații clare, profesioniste și ușor de înțeles poate transforma complet modul în care oamenii iau decizii în piața imobiliară. Iar misiunea noastră este să facem aceste informații accesibile tuturor.
                </p>

                <p class="text-[15px] leading-[1.78] text-brand-primary/78 md:text-base md:leading-[1.82]">
                    Ne străduim în fiecare zi să construim un produs de care să fim mândri și care să aducă valoare reală utilizatorilor noștri. Dezvoltăm continuu baza noastră de date și procesele de analiză, iar fiecare opinie, sugestie sau observație primită din partea clienților este atent analizată și luată în considerare. Feedback-ul vostru ne ajută să devenim mai buni și să construim împreună un serviciu tot mai util și mai performant!
                </p>

                <p class="text-lg font-semibold text-brand-primary">
                    Vă mulțumim!
                </p>

                <div class="flex flex-col items-start md:items-end">
                    <h2 class="text-[1.6rem] font-bold leading-[1.08] tracking-[-0.035em] text-brand-primary">
                        Echipa noastră
                    </h2>
                    <div class="mt-5 flex flex-col items-start gap-4 text-[15px] leading-[1.7] text-brand-primary/78 md:flex-row md:items-end md:justify-end md:gap-8">
                        <p class="min-w-28 text-left md:text-right"><span class="block text-sm font-semibold text-brand-primary">Founder</span> Cosmin M.</p>
                        <p class="min-w-36 text-left md:text-right"><span class="block text-sm font-semibold text-brand-primary">Co-founder</span> Arhitect Andrei P.</p>
                        <p class="min-w-28 text-left md:text-right"><span class="block text-sm font-semibold text-brand-primary">IT - software</span> Mathias M.</p>
                    </div>

                    <img src="{{ asset('images/signature.png') }}" alt="Semnătura echipei GetYourConsultant" class="mt-8 h-auto w-64 object-contain md:w-64">
                </div>
            </div>
        </div>
    </section>
</x-layouts.public-marketing>
