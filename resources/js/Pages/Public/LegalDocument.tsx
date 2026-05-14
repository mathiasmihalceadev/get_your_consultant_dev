import { Head, usePage } from "@inertiajs/react";
import ReactMarkdown, { type Components } from "react-markdown";
import remarkGfm from "remark-gfm";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { PageProps } from "@/types";

interface LegalDocumentProps {
    title: string;
    description: string;
    markdown: string;
}

const markdownComponents: Components = {
    h1: ({ node, ...props }) => (
        <h1
            className="text-[2rem] font-bold leading-[1.02] tracking-[-0.04em] text-brand-primary md:text-[2.8rem]"
            {...props}
        />
    ),
    h2: ({ node, ...props }) => (
        <h2
            className="mt-10 text-[1.4rem] font-semibold leading-[1.08] tracking-[-0.035em] text-brand-primary md:text-[1.9rem]"
            {...props}
        />
    ),
    h3: ({ node, ...props }) => (
        <h3
            className="mt-8 text-[1.02rem] font-semibold leading-[1.28] text-brand-primary md:text-[1.22rem]"
            {...props}
        />
    ),
    p: ({ node, ...props }) => (
        <p
            className="mt-4 text-[14px] leading-[1.72] text-brand-primary/78 md:text-base"
            {...props}
        />
    ),
    ul: ({ node, ...props }) => (
        <ul
            className="mt-4 list-disc space-y-2 pl-5 text-[14px] leading-[1.72] text-brand-primary/78 md:text-base"
            {...props}
        />
    ),
    ol: ({ node, ...props }) => (
        <ol
            className="mt-4 list-decimal space-y-2 pl-5 text-[14px] leading-[1.72] text-brand-primary/78 md:text-base"
            {...props}
        />
    ),
    li: ({ node, ...props }) => <li className="pl-1" {...props} />,
    hr: () => <div className="my-8 h-px bg-brand-primary/10" />,
    blockquote: ({ node, ...props }) => (
        <blockquote
            className="mt-6 border-l-2 border-brand-secondary/35 bg-[#f6f8ff] px-4 py-3 text-[14px] leading-[1.7] text-brand-primary/80 md:text-base"
            {...props}
        />
    ),
    strong: ({ node, ...props }) => (
        <strong className="font-semibold text-brand-primary" {...props} />
    ),
    em: ({ node, ...props }) => <em className="italic" {...props} />,
    a: ({ node, href, ...props }) => {
        const isExternal = Boolean(href?.startsWith("http"));

        return (
            <a
                href={href}
                className="font-semibold text-brand-secondary underline decoration-brand-secondary/30 underline-offset-3 transition-colors hover:text-brand-primary"
                target={isExternal ? "_blank" : undefined}
                rel={isExternal ? "noreferrer" : undefined}
                {...props}
            />
        );
    },
    table: ({ node, children, ...props }) => (
        <div className="my-6 overflow-x-auto border solid-border solid-border-warm bg-white">
            <table
                className="min-w-full border-collapse text-left text-[13px] leading-[1.65] text-brand-primary/80 md:text-[15px]"
                {...props}
            >
                {children}
            </table>
        </div>
    ),
    thead: ({ node, ...props }) => (
        <thead className="bg-[#f4f6ff]" {...props} />
    ),
    tbody: ({ node, ...props }) => <tbody {...props} />,
    tr: ({ node, ...props }) => (
        <tr className="border-t border-brand-primary/10" {...props} />
    ),
    th: ({ node, ...props }) => (
        <th
            className="px-4 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-brand-primary/76 md:text-[13px]"
            {...props}
        />
    ),
    td: ({ node, ...props }) => (
        <td className="px-4 py-3 align-top text-brand-primary/78" {...props} />
    ),
};

export default function LegalDocument({
    title,
    description,
    markdown,
}: LegalDocumentProps) {
    const { locale } = useTranslation();
    const {
        seo: { canonical },
    } = usePage<PageProps>().props;
    const socialTitle = `${title} | Get Your Consultant`;
    const schema = JSON.stringify({
        "@context": "https://schema.org",
        "@type": "WebPage",
        name: title,
        description,
        inLanguage: locale === "ro" ? "ro-RO" : "en-US",
        url: canonical ?? undefined,
    });

    return (
        <PublicLayout>
            <Head title={title}>
                <meta
                    head-key="description"
                    name="description"
                    content={description}
                />
                <meta
                    head-key="og:title"
                    property="og:title"
                    content={socialTitle}
                />
                <meta
                    head-key="og:description"
                    property="og:description"
                    content={description}
                />
                <meta head-key="og:type" property="og:type" content="website" />
                <meta
                    head-key="og:site_name"
                    property="og:site_name"
                    content="Get Your Consultant"
                />
                <meta
                    head-key="og:locale"
                    property="og:locale"
                    content={locale === "ro" ? "ro_RO" : "en_US"}
                />
                {canonical ? (
                    <meta
                        head-key="og:url"
                        property="og:url"
                        content={canonical}
                    />
                ) : null}
                <meta
                    head-key="twitter:card"
                    name="twitter:card"
                    content="summary"
                />
                <meta
                    head-key="twitter:title"
                    name="twitter:title"
                    content={socialTitle}
                />
                <meta
                    head-key="twitter:description"
                    name="twitter:description"
                    content={description}
                />
                <script
                    head-key="legal-schema"
                    type="application/ld+json"
                    dangerouslySetInnerHTML={{ __html: schema }}
                />
            </Head>

            <section className="relative overflow-hidden bg-[linear-gradient(180deg,#eef2ff_0%,#ffffff_18rem)] py-12 md:py-16">
                <div className="pointer-events-none absolute inset-x-0 top-0 h-40 bg-[radial-gradient(circle_at_top,rgba(115,128,217,0.18),transparent_72%)]" />

                <div className="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    <div className="border solid-border solid-border-warm bg-white p-6 shadow-[0_18px_44px_rgba(52,48,106,0.06)] md:p-8 lg:p-10">
                        <ReactMarkdown
                            remarkPlugins={[remarkGfm]}
                            components={markdownComponents}
                        >
                            {markdown}
                        </ReactMarkdown>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
