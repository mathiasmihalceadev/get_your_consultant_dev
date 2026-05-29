@php
    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $title,
        'description' => $description,
        'inLanguage' => app()->getLocale() === 'ro' ? 'ro-RO' : 'en-US',
        'url' => $canonical,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@push('head')
    <script type="application/ld+json">{!! $schema !!}</script>
@endpush

<x-layouts.public-marketing
    :title="$title.' | Get Your Consultant'"
    :description="$description"
    :canonical="$canonical"
    :alternates="$alternates"
    :x-default="$xDefault"
>
    <section class="relative overflow-hidden bg-[linear-gradient(180deg,#eef2ff_0%,#ffffff_18rem)] py-12 md:py-16">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-[radial-gradient(circle_at_top,rgba(115,128,217,0.18),transparent_72%)]"></div>

        <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="border solid-border solid-border-warm bg-white p-6 shadow-[0_18px_44px_rgba(52,48,106,0.06)] md:p-8 lg:p-10">
                <article class="text-brand-primary [&_h1]:text-[2rem] [&_h1]:font-bold [&_h1]:leading-[1.02] [&_h1]:tracking-[-0.04em] md:[&_h1]:text-[2.8rem] [&_h2]:mt-10 [&_h2]:text-[1.4rem] [&_h2]:font-semibold [&_h2]:leading-[1.08] [&_h2]:tracking-[-0.035em] md:[&_h2]:text-[1.9rem] [&_h3]:mt-8 [&_h3]:text-[1.02rem] [&_h3]:font-semibold [&_h3]:leading-[1.28] md:[&_h3]:text-[1.22rem] [&_p]:mt-4 [&_p]:text-[14px] [&_p]:leading-[1.72] [&_p]:text-brand-primary/78 md:[&_p]:text-base [&_ul]:mt-4 [&_ul]:list-disc [&_ul]:space-y-2 [&_ul]:pl-5 [&_ul]:text-[14px] [&_ul]:leading-[1.72] [&_ul]:text-brand-primary/78 md:[&_ul]:text-base [&_ol]:mt-4 [&_ol]:list-decimal [&_ol]:space-y-2 [&_ol]:pl-5 [&_ol]:text-[14px] [&_ol]:leading-[1.72] [&_ol]:text-brand-primary/78 md:[&_ol]:text-base [&_li]:pl-1 [&_hr]:my-8 [&_hr]:h-px [&_hr]:border-0 [&_hr]:bg-brand-primary/10 [&_blockquote]:mt-6 [&_blockquote]:border-l-2 [&_blockquote]:border-brand-secondary/35 [&_blockquote]:bg-[#f6f8ff] [&_blockquote]:px-4 [&_blockquote]:py-3 [&_blockquote]:text-[14px] [&_blockquote]:leading-[1.7] [&_blockquote]:text-brand-primary/80 md:[&_blockquote]:text-base [&_strong]:font-semibold [&_strong]:text-brand-primary [&_em]:italic [&_a]:font-semibold [&_a]:text-brand-secondary [&_a]:underline [&_a]:decoration-brand-secondary/30 [&_a]:underline-offset-3 hover:[&_a]:text-brand-primary [&_table]:mt-6 [&_table]:w-full [&_table]:border-collapse [&_table]:text-left [&_table]:text-[13px] [&_table]:leading-[1.65] [&_table]:text-brand-primary/80 md:[&_table]:text-[15px] [&_thead]:bg-[#f4f6ff] [&_tr]:border-t [&_tr]:border-brand-primary/10 [&_th]:px-4 [&_th]:py-3 [&_th]:text-[12px] [&_th]:font-semibold [&_th]:uppercase [&_th]:tracking-[0.08em] [&_th]:text-brand-primary/76 md:[&_th]:text-[13px] [&_td]:px-4 [&_td]:py-3 [&_td]:align-top [&_td]:text-brand-primary/78">
                    {!! $contentHtml !!}
                </article>
            </div>
        </div>
    </section>
</x-layouts.public-marketing>
