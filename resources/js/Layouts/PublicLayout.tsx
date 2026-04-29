import { Link, router } from "@inertiajs/react";
import {
    Globe,
    FacebookLogo,
    InstagramLogo,
    LinkedinLogo,
} from "@phosphor-icons/react";
import { PropsWithChildren } from "react";
import { useTranslation } from "@/hooks/useTranslation";

export default function PublicLayout({ children }: PropsWithChildren) {
    const { t, locale, localePath } = useTranslation();
    const otherLocale = locale === "en" ? "ro" : "en";
    const navItems = [
        { key: "how_it_works", href: localePath("/#how-it-works") },
        {
            key: "landing_nav_features",
            href: localePath("/#report-content"),
        },
        {
            key: "landing_nav_comparison",
            href: localePath("/#consultant-vs-report"),
        },
        { key: "pricing", href: localePath("/#pricing") },
        { key: "contact", href: localePath("/#contact") },
    ];

    const switchLocale = () => {
        const currentPath = window.location.pathname;
        const newPath = currentPath.replace(`/${locale}`, `/${otherLocale}`);
        router.visit(newPath);
    };

    return (
        <div className="min-h-screen flex flex-col bg-white">
            {/* Top accent line */}
            <div className="h-[3px] bg-brand-secondary" />

            {/* Header */}
            <header className="border-b border-gray-200 bg-white sticky top-0 z-50">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-3">
                    <div className="flex items-center justify-between gap-4">
                        <Link
                            href={localePath("/")}
                            className="flex items-center gap-2.5 text-brand-primary font-bold text-lg hover:opacity-80 transition-opacity"
                        >
                            <img
                                className="h-10 w-auto object-contain"
                                src="/images/logo-white.jpg"
                            />
                        </Link>

                        <div className="flex items-center gap-2.5">
                            <Link
                                href={localePath("/get-report")}
                                className="inline-flex items-center justify-center bg-brand-secondary text-white text-sm font-semibold px-4 py-2 hover:bg-brand-secondary/90 transition-colors"
                            >
                                {t("get_report")}
                            </Link>

                            <button
                                onClick={switchLocale}
                                className="flex items-center gap-1.5 text-sm font-semibold text-brand-neutral hover:text-brand-primary transition-colors cursor-pointer border border-gray-200 px-3 py-2"
                            >
                                <Globe size={16} />
                                {otherLocale.toUpperCase()}
                            </button>
                        </div>
                    </div>

                    <nav className="mt-3 -mx-1 flex items-center gap-1 overflow-x-auto pb-1">
                        {navItems.map((item) => (
                            <a
                                key={item.key}
                                href={item.href}
                                className="shrink-0 px-3 py-1.5 text-xs sm:text-sm font-medium text-brand-neutral hover:text-brand-primary hover:bg-gray-100 transition-colors"
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
                            <p className="text-sm text-white/60 max-w-md">
                                {t("landing_cta_desc")}
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
                                    className="w-9 h-9 border border-white/20 text-white/80 hover:text-white hover:border-white/40 flex items-center justify-center transition-colors"
                                >
                                    <FacebookLogo size={18} weight="duotone" />
                                </a>
                                <a
                                    href="https://www.instagram.com"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Instagram"
                                    className="w-9 h-9 border border-white/20 text-white/80 hover:text-white hover:border-white/40 flex items-center justify-center transition-colors"
                                >
                                    <InstagramLogo size={18} weight="duotone" />
                                </a>
                                <a
                                    href="https://www.linkedin.com"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="LinkedIn"
                                    className="w-9 h-9 border border-white/20 text-white/80 hover:text-white hover:border-white/40 flex items-center justify-center transition-colors"
                                >
                                    <LinkedinLogo size={18} weight="duotone" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <div className="border-t border-white/10 pt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div className="flex flex-wrap gap-4 text-sm">
                            <a
                                href={localePath("/#contact")}
                                className="text-white/60 hover:text-white transition-colors"
                            >
                                {t("contact")}
                            </a>
                            <a
                                href={localePath("/#privacy-policy")}
                                className="text-white/60 hover:text-white transition-colors"
                            >
                                {t("privacy")}
                            </a>
                            <a
                                href={localePath("/#terms-and-conditions")}
                                className="text-white/60 hover:text-white transition-colors"
                            >
                                {t("terms")}
                            </a>
                            <a
                                href={localePath("/#cookie-policy")}
                                className="text-white/60 hover:text-white transition-colors"
                            >
                                {t("cookie_policy")}
                            </a>
                        </div>

                        <p className="text-sm text-white/40">
                            &copy; {new Date().getFullYear()} {t("site_name")}.{" "}
                            {t("all_rights_reserved")}
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
