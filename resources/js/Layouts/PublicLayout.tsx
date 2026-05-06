import { Link } from "@inertiajs/react";
import {
    Globe,
    FacebookLogo,
    InstagramLogo,
    LinkedinLogo,
} from "@phosphor-icons/react";
import { PropsWithChildren } from "react";
import { useTranslation } from "@/hooks/useTranslation";

export default function PublicLayout({ children }: PropsWithChildren) {
    const { t, locale, localePath, localizedUrls } = useTranslation();
    const otherLocale = locale === "en" ? "ro" : "en";
    const navItems = [
        {
            key: "landing_nav_example",
            href: localePath("/#report-example"),
        },
        {
            key: "landing_nav_problem",
            href: localePath("/#problem"),
        },
        { key: "how_it_works", href: localePath("/#how-it-works") },
        { key: "pricing", href: localePath("/#pricing") },
        { key: "landing_nav_trust", href: localePath("/#trust") },
    ];

    const switchLocale = () => {
        const targetUrl = localizedUrls?.[otherLocale];

        if (!targetUrl) {
            return;
        }

        window.location.assign(`${targetUrl}${window.location.hash || ""}`);
    };

    return (
        <div className="min-h-screen flex flex-col bg-white">
            {/* Top accent line */}
            <div className="h-[3px] bg-brand-secondary" />

            {/* Header */}
            <header className="border-b border-gray-200 bg-white sticky top-0 z-50">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-3">
                    <div className="flex items-center justify-between gap-4 lg:gap-6">
                        <Link
                            href={localePath("/")}
                            className="shrink-0 flex items-center gap-2.5 text-brand-primary font-bold text-lg hover:opacity-80 transition-opacity"
                        >
                            <img
                                className="h-10 w-auto object-contain"
                                src="/images/logo-white.jpg"
                            />
                        </Link>

                        <nav className="hidden lg:flex min-w-0 flex-1 items-center justify-center gap-1 px-2">
                            {navItems.map((item) => (
                                <a
                                    key={item.key}
                                    href={item.href}
                                    className="truncate px-3 py-1.5 text-sm font-medium text-brand-primary hover:text-brand-primary hover:bg-[#fcfaf6] transition-colors"
                                >
                                    {t(item.key)}
                                </a>
                            ))}
                        </nav>

                        <div className="shrink-0 flex items-center gap-2.5">
                            <Link
                                href={localePath("/get-report")}
                                className="inline-flex items-center justify-center bg-brand-secondary text-white text-sm font-semibold px-4 py-2 hover:bg-brand-secondary/90 transition-colors"
                            >
                                {t("get_report")}
                            </Link>

                            <button
                                onClick={switchLocale}
                                className="flex items-center gap-1.5 text-sm font-semibold text-brand-primary hover:text-brand-primary transition-colors cursor-pointer border border-gray-200 px-3 py-2"
                            >
                                <Globe size={16} />
                                {otherLocale.toUpperCase()}
                            </button>
                        </div>
                    </div>

                    <nav className="mt-3 -mx-1 flex items-center gap-1 overflow-x-auto pb-1 lg:hidden">
                        {navItems.map((item) => (
                            <a
                                key={item.key}
                                href={item.href}
                                className="shrink-0 px-3 py-1.5 text-xs sm:text-sm font-medium text-brand-primary hover:text-brand-primary hover:bg-[#fcfaf6] transition-colors"
                            >
                                {t(item.key)}
                            </a>
                        ))}
                    </nav>
                </div>
            </header>

            {/* Main content */}
            <main className="flex-1">{children}</main>

            {/* Footer */}
            <footer className="bg-brand-primary">
                <div className="h-[3px] bg-brand-secondary" />
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
                    <div className="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8 mb-8">
                        <div>
                            <img
                                src="/images/logo-dark.jpg"
                                alt={t("site_name")}
                                className="h-10 w-auto object-contain mb-4"
                            />
                            <p className="max-w-md text-sm text-white/80">
                                {t("landing_footer_desc")}
                            </p>
                        </div>

                        <div>
                            <h4 className="text-xs font-bold text-brand-secondary uppercase tracking-widest mb-3">
                                {t("footer_follow_us")}
                            </h4>
                            <div className="flex items-center gap-2">
                                <a
                                    href="https://www.facebook.com"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Facebook"
                                    className="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white"
                                >
                                    <FacebookLogo size={18} weight="duotone" />
                                </a>
                                <a
                                    href="https://www.instagram.com"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Instagram"
                                    className="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white"
                                >
                                    <InstagramLogo size={18} weight="duotone" />
                                </a>
                                <a
                                    href="https://www.linkedin.com"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="LinkedIn"
                                    className="flex h-9 w-9 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/40 hover:text-white"
                                >
                                    <LinkedinLogo size={18} weight="duotone" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-col gap-4 border-t border-white/10 pt-6 md:flex-row md:items-center md:justify-between">
                        <div className="flex flex-wrap gap-4 text-sm">
                            <a
                                href={localePath("/#hero-form")}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("get_report")}
                            </a>
                            <a
                                href={localePath("/#privacy-policy")}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("privacy")}
                            </a>
                            <a
                                href={localePath("/#terms-and-conditions")}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("terms")}
                            </a>
                            <a
                                href={localePath("/#cookie-policy")}
                                className="text-white/70 transition-colors hover:text-white"
                            >
                                {t("cookie_policy")}
                            </a>
                        </div>

                        <p className="text-sm text-white/50">
                            &copy; {new Date().getFullYear()} {t("site_name")}.{" "}
                            {t("all_rights_reserved")}
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
