import { Link } from "@inertiajs/react";
import {
    Globe,
    FacebookLogo,
    InstagramLogo,
    TiktokLogoIcon,
} from "@phosphor-icons/react";
import { PropsWithChildren } from "react";
import { useTranslation } from "@/hooks/useTranslation";

export default function PublicLayout({ children }: PropsWithChildren) {
    const {
        t,
        locale,
        localePath,
        localizedUrls,
        publicLocales,
        showLocaleSwitcher,
    } = useTranslation();
    const otherLocale =
        publicLocales.find((candidateLocale) => candidateLocale !== locale) ??
        null;
    const currentYear = new Date().getFullYear();
    const legalPaths =
        locale === "ro"
            ? {
                  privacy: "/politica-de-confidentialitate",
                  terms: "/termeni-si-conditii",
                  cookies: "/politica-de-cookie-uri",
              }
            : {
                  privacy: "/privacy-policy",
                  terms: "/terms-and-conditions",
                  cookies: "/cookie-policy",
              };
    const navItems = [
        {
            key: "landing_nav_example",
            href: localePath("/#report-example"),
        },
        { key: "how_it_works", href: localePath("/#how-it-works") },
        { key: "landing_nav_reviews", href: localePath("/#reviews") },
        { key: "pricing", href: localePath("/#pricing") },
        { key: "contact", href: localePath("/contact") },
    ];

    const switchLocale = () => {
        if (!otherLocale) {
            return;
        }

        const targetUrl = localizedUrls?.[otherLocale];

        if (!targetUrl) {
            return;
        }

        window.location.assign(`${targetUrl}${window.location.hash || ""}`);
    };

    return (
        <div className="min-h-screen flex flex-col bg-white">
            {/* Top accent line */}
            <div className="h-0.75 bg-brand-secondary" />

            {/* Header */}
            <header className="border-b border-gray-200 bg-white sticky top-0 z-50">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-3">
                    <div className="flex items-center justify-between gap-4 lg:gap-6">
                        <a
                            href={localePath("/")}
                            className="shrink-0 flex items-center gap-2.5 text-brand-primary font-bold text-lg hover:opacity-80 transition-opacity"
                        >
                            <img
                                className="h-10 w-auto object-contain md:h-12"
                                src="/images/logo-white.jpg"
                            />
                        </a>

                        <nav className="hidden lg:flex min-w-0 flex-1 items-center justify-center gap-1 px-2">
                            {navItems.map((item) => (
                                <a
                                    key={item.key}
                                    href={item.href}
                                    className="truncate px-3 py-1.5 text-[14px] font-semibold text-brand-primary/76 hover:text-brand-primary hover:bg-[#eef1ff] transition-colors md:text-base"
                                >
                                    {t(item.key)}
                                </a>
                            ))}
                        </nav>

                        <div className="shrink-0 flex items-center gap-2.5">
                            <Link
                                href={localePath("/get-report")}
                                className="inline-flex items-center justify-center bg-brand-primary px-4 py-2 text-[14px] font-semibold text-white hover:bg-brand-primary/90 transition-colors md:text-base"
                            >
                                {t("get_report")}
                            </Link>

                            {showLocaleSwitcher && otherLocale ? (
                                <button
                                    onClick={switchLocale}
                                    className="flex items-center gap-1.5 border border-gray-200 px-3 py-2 text-[14px] font-semibold text-brand-primary/76 transition-colors cursor-pointer hover:text-brand-primary md:text-base"
                                >
                                    <Globe size={16} />
                                    {otherLocale.toUpperCase()}
                                </button>
                            ) : null}
                        </div>
                    </div>

                    <nav className="mt-3 -mx-1 flex items-center gap-1 overflow-x-auto pb-1 lg:hidden">
                        {navItems.map((item) => (
                            <a
                                key={item.key}
                                href={item.href}
                                className="shrink-0 px-3 py-1.5 text-[14px] font-semibold text-brand-primary/76 hover:text-brand-primary hover:bg-[#eef1ff] transition-colors md:text-base"
                            >
                                {t(item.key)}
                            </a>
                        ))}
                    </nav>
                </div>
            </header>

            {/* Main content */}
            <main className="flex flex-1 flex-col">{children}</main>

            {/* Footer */}
            <footer className="bg-brand-primary">
                <div className="h-0.75 bg-brand-secondary" />
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
                    <div className="mb-8 grid gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(220px,0.7fr)_minmax(0,0.95fr)] lg:items-start">
                        <div>
                            <img
                                src="/images/logo-footer.png"
                                alt={t("site_name")}
                                className="mb-4 h-18 w-auto object-contain md:h-22"
                            />
                            <p className="max-w-md text-[12px] text-white/60 md:text-[14px]">
                                {t("landing_footer_desc")}
                            </p>
                        </div>

                        <div className="flex flex-col gap-4 lg:items-center lg:justify-end">
                            <a
                                href="https://reclamatiisal.anpc.ro/"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="block transition-colors"
                                aria-label="ANPC"
                            >
                                <img
                                    src="/images/anpc.png"
                                    alt="ANPC"
                                    className="h-auto w-full max-w-64 object-contain md:max-w-84"
                                />
                            </a>
                            <a
                                href="https://consumer-redress.ec.europa.eu/index_en?prefLang=ro"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="block transition-colors"
                                aria-label="Online dispute resolution"
                            >
                                <img
                                    src="/images/solutionare.png"
                                    alt="Online dispute resolution"
                                    className="h-auto w-full max-w-64 object-contain md:max-w-84"
                                />
                            </a>
                        </div>

                        <div className="lg:justify-self-end lg:text-right">
                            <h4 className="mb-3 text-[14px] text-white/80 md:text-base">
                                {t("footer_follow_us")}
                            </h4>
                            <div className="flex items-center gap-2 lg:justify-end">
                                <a
                                    href="https://www.facebook.com/profile.php?id=61590563915563&locale=ro_RO"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Facebook"
                                    className="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white"
                                >
                                    <FacebookLogo size={18} weight="duotone" />
                                </a>
                                <a
                                    href="https://www.instagram.com/getyourconsultant"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Instagram"
                                    className="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white"
                                >
                                    <InstagramLogo size={18} weight="duotone" />
                                </a>
                                <a
                                    href="https://www.tiktok.com/@getyourconsultant"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="TikTok"
                                    className="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white"
                                >
                                    <TiktokLogoIcon
                                        size={18}
                                        weight="duotone"
                                    />
                                </a>
                            </div>
                            <div className="mt-4">
                                <p className="whitespace-pre-line text-[14px] leading-[1.7] text-white/70 md:text-base">
                                    {t("landing_footer_address")}
                                </p>
                            </div>
                            <img
                                src="/images/secure-payment.png"
                                alt="Secure payment"
                                className="mt-6 h-auto w-full max-w-64 object-contain md:max-w-64"
                            />
                        </div>
                    </div>

                    <div className="flex flex-col gap-4 border-t border-white/10 pt-6 md:flex-row md:items-end md:justify-between">
                        <div className="flex flex-wrap gap-4 text-[14px] md:text-base">
                            <a
                                href={localePath("/#hero-form")}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("get_report")}
                            </a>
                            <a
                                href={localePath(legalPaths.privacy)}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("privacy")}
                            </a>
                            <a
                                href={localePath(legalPaths.terms)}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("terms")}
                            </a>
                            <a
                                href={localePath(legalPaths.cookies)}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("cookie_policy")}
                            </a>
                            <a
                                href={localePath("/contact")}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("contact")}
                            </a>
                        </div>

                        <div className="space-y-1 text-[13px] leading-[1.55] text-white/56 md:ml-auto md:max-w-none md:shrink-0 md:text-right md:text-sm md:leading-[1.65] lg:whitespace-nowrap">
                            <p>{t("landing_footer_legal_note")}</p>
                            <p>
                                &copy; {currentYear} {t("site_name")}.{" "}
                                {t("landing_footer_copyright_suffix")}
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
