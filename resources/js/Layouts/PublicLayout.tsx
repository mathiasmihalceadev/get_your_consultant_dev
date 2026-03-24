import { Link, router } from "@inertiajs/react";
import { House, Globe } from "@phosphor-icons/react";
import { PropsWithChildren } from "react";
import { useTranslation } from "@/hooks/useTranslation";

export default function PublicLayout({ children }: PropsWithChildren) {
    const { t, locale, localePath } = useTranslation();
    const otherLocale = locale === "en" ? "ro" : "en";

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
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div className="flex h-16 items-center justify-between">
                        <Link
                            href={localePath("/")}
                            className="flex items-center gap-2.5 text-brand-primary font-bold text-lg hover:opacity-80 transition-opacity"
                        >
                            <img
                                className="h-8 w-auto object-contain"
                                src="/images/logo-white.jpg"
                            />
                        </Link>

                        <nav className="hidden md:flex items-center gap-7">
                            <Link
                                href={localePath("/")}
                                className="text-sm font-semibold text-brand-primary hover:text-brand-secondary transition-colors"
                            >
                                {t("get_report")}
                            </Link>
                            <span className="text-sm font-medium text-brand-neutral hover:text-brand-primary transition-colors cursor-pointer">
                                {t("how_it_works")}
                            </span>
                            <span className="text-sm font-medium text-brand-neutral hover:text-brand-primary transition-colors cursor-pointer">
                                {t("pricing")}
                            </span>
                            <span className="text-sm font-medium text-brand-neutral hover:text-brand-primary transition-colors cursor-pointer">
                                {t("faq")}
                            </span>
                            <span className="text-sm font-medium text-brand-neutral hover:text-brand-primary transition-colors cursor-pointer">
                                {t("contact")}
                            </span>
                        </nav>

                        <button
                            onClick={switchLocale}
                            className="flex items-center gap-1.5 text-sm font-semibold text-brand-neutral hover:text-brand-primary transition-colors cursor-pointer border border-gray-200 px-3 py-1.5"
                        >
                            <Globe size={16} />
                            {otherLocale.toUpperCase()}
                        </button>
                    </div>
                </div>
            </header>

            {/* Main content */}
            <main className="flex-1">{children}</main>

            {/* Footer */}
            <footer className="bg-brand-primary">
                <div className="h-[3px] bg-brand-secondary" />
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-12">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
                        <div>
                            <h4 className="text-xs font-bold text-brand-secondary uppercase tracking-widest mb-4">
                                {t("company")}
                            </h4>
                            <ul className="space-y-2.5">
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("about")}
                                    </span>
                                </li>
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("careers")}
                                    </span>
                                </li>
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("contact")}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-xs font-bold text-brand-secondary uppercase tracking-widest mb-4">
                                {t("resources")}
                            </h4>
                            <ul className="space-y-2.5">
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("blog")}
                                    </span>
                                </li>
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("how_it_works")}
                                    </span>
                                </li>
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("pricing")}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-xs font-bold text-brand-secondary uppercase tracking-widest mb-4">
                                {t("support")}
                            </h4>
                            <ul className="space-y-2.5">
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("help_center")}
                                    </span>
                                </li>
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("faq")}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-xs font-bold text-brand-secondary uppercase tracking-widest mb-4">
                                {t("legal")}
                            </h4>
                            <ul className="space-y-2.5">
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("terms")}
                                    </span>
                                </li>
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("privacy")}
                                    </span>
                                </li>
                                <li>
                                    <span className="text-sm text-white/60 hover:text-white transition-colors cursor-pointer">
                                        {t("cookie_policy")}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div className="flex items-center gap-2.5 text-white font-bold">
                            <div className="w-7 h-7 bg-brand-secondary flex items-center justify-center">
                                <House
                                    size={14}
                                    weight="fill"
                                    className="text-white"
                                />
                            </div>
                            {t("site_name")}
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
