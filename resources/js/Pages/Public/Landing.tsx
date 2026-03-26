import { Head, Link } from "@inertiajs/react";
import {
    ArrowRight,
    ChartLineUp,
    ShieldCheck,
    MagnifyingGlass,
    EnvelopeSimple,
    FilePdf,
    House,
    Storefront,
    Key,
    ShoppingCart,
    Scales,
    MapPin,
    Pulse,
} from "@phosphor-icons/react";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { motion } from "framer-motion";

/* ── Spring-based animation variants ── */
const spring = { type: "spring" as const, stiffness: 100, damping: 20 };

const staggerContainer = {
    hidden: {},
    visible: {
        transition: { staggerChildren: 0.1 },
    },
};

const slideUp = {
    hidden: { opacity: 0, y: 32 },
    visible: {
        opacity: 1,
        y: 0,
        transition: spring,
    },
};

const slideRight = {
    hidden: { opacity: 0, x: -40 },
    visible: {
        opacity: 1,
        x: 0,
        transition: spring,
    },
};

const slideLeft = {
    hidden: { opacity: 0, x: 40 },
    visible: {
        opacity: 1,
        x: 0,
        transition: spring,
    },
};

const scaleIn = {
    hidden: { opacity: 0, scale: 0.92 },
    visible: {
        opacity: 1,
        scale: 1,
        transition: spring,
    },
};

export default function Landing() {
    const { t, localePath } = useTranslation();

    const reportTypes = [
        {
            icon: Key,
            labelKey: "type_rental_living",
            descKey: "rental_living_desc",
            type: "rental_living",
        },
        {
            icon: Storefront,
            labelKey: "type_rental_business",
            descKey: "rental_business_desc",
            type: "rental_business",
        },
        {
            icon: House,
            labelKey: "type_buying_living",
            descKey: "buying_living_desc",
            type: "buying_living",
        },
        {
            icon: ShoppingCart,
            labelKey: "type_buying_business",
            descKey: "buying_business_desc",
            type: "buying_business",
        },
    ];

    const steps = [
        {
            num: "01",
            icon: MagnifyingGlass,
            titleKey: "landing_step1_title",
            descKey: "landing_step1_desc",
        },
        {
            num: "02",
            icon: EnvelopeSimple,
            titleKey: "landing_step2_title",
            descKey: "landing_step2_desc",
        },
        {
            num: "03",
            icon: FilePdf,
            titleKey: "landing_step3_title",
            descKey: "landing_step3_desc",
        },
    ];

    return (
        <PublicLayout>
            <Head title={t("landing_meta_title")} />

            {/* ── HERO — Split-screen, left-aligned ── */}
            <section className="relative overflow-hidden bg-[#fafaf9] min-h-dvh flex items-center">
                {/* Subtle noise overlay */}
                <div
                    className="pointer-events-none fixed inset-0 z-50 opacity-[0.025]"
                    style={{
                        backgroundImage: `url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")`,
                    }}
                />

                <div className="relative mx-auto w-full max-w-350 px-6 md:px-10 lg:px-16 py-20 md:py-0">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-8 items-center">
                        {/* Left — Text */}
                        <motion.div
                            initial="hidden"
                            animate="visible"
                            variants={staggerContainer}
                            className="max-w-xl"
                        >
                            <motion.p
                                variants={slideUp}
                                className="text-xs font-semibold tracking-[0.2em] uppercase text-brand-secondary mb-6"
                            >
                                {t("landing_badge")}
                            </motion.p>

                            <motion.h1
                                variants={slideUp}
                                className="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tighter leading-none text-brand-primary mb-6"
                            >
                                {t("landing_hero_title_1")}
                                <br />
                                <span className="text-brand-secondary">
                                    {t("landing_hero_title_2")}
                                </span>
                            </motion.h1>

                            <motion.p
                                variants={slideUp}
                                className="text-base text-slate-500 leading-relaxed max-w-[55ch] mb-10"
                            >
                                {t("landing_hero_desc")}
                            </motion.p>

                            <motion.div
                                variants={slideUp}
                                className="flex flex-wrap items-center gap-4"
                            >
                                <Link
                                    href={localePath("/get-report")}
                                    className="group inline-flex items-center gap-2.5 bg-brand-primary text-white font-medium px-7 py-3.5 text-sm transition-all duration-200 hover:scale-[0.98] active:scale-[0.96]"
                                >
                                    {t("landing_cta_primary")}
                                    <ArrowRight
                                        size={15}
                                        weight="bold"
                                        className="transition-transform duration-200 group-hover:translate-x-0.5"
                                    />
                                </Link>
                                <a
                                    href="#how-it-works"
                                    className="inline-flex items-center gap-2 text-sm font-medium text-brand-neutral hover:text-brand-primary transition-colors duration-200"
                                >
                                    {t("landing_cta_secondary")}
                                </a>
                            </motion.div>

                            <motion.div
                                variants={slideUp}
                                className="mt-12 flex items-center gap-6 text-xs text-slate-400"
                            >
                                {[
                                    t("landing_trust_1"),
                                    t("landing_trust_2"),
                                    t("landing_trust_3"),
                                ].map((item, i) => (
                                    <span
                                        key={i}
                                        className="flex items-center gap-1.5"
                                    >
                                        <span className="w-1 h-1 rounded-full bg-brand-secondary" />
                                        {item}
                                    </span>
                                ))}
                            </motion.div>
                        </motion.div>

                        {/* Right — Asymmetric bento preview */}
                        <motion.div
                            initial="hidden"
                            animate="visible"
                            variants={staggerContainer}
                            className="hidden md:grid grid-cols-2 gap-3"
                        >
                            {reportTypes.map(
                                (
                                    { icon: Icon, labelKey, descKey, type },
                                    i,
                                ) => (
                                    <motion.div key={type} variants={scaleIn}>
                                        <Link
                                            href={localePath(
                                                `/get-report?type=${type}`,
                                            )}
                                            className={`group block bg-white border border-slate-200/60 p-6 transition-all duration-300 hover:border-brand-secondary/30 hover:shadow-[0_8px_30px_-12px_rgba(245,145,93,0.15)] ${
                                                i === 0
                                                    ? "col-span-1 row-span-1"
                                                    : ""
                                            }`}
                                        >
                                            <Icon
                                                size={24}
                                                weight="duotone"
                                                className="text-brand-primary mb-4 transition-colors duration-200 group-hover:text-brand-secondary"
                                            />
                                            <h3 className="font-semibold text-brand-primary text-sm tracking-tight mb-1.5">
                                                {t(labelKey)}
                                            </h3>
                                            <p className="text-xs text-slate-400 leading-relaxed">
                                                {t(descKey)}
                                            </p>
                                            <div className="mt-4 flex items-center gap-1 text-xs font-medium text-brand-secondary opacity-0 translate-y-1 transition-all duration-200 group-hover:opacity-100 group-hover:translate-y-0">
                                                {t("landing_start_report")}
                                                <ArrowRight
                                                    size={11}
                                                    weight="bold"
                                                />
                                            </div>
                                        </Link>
                                    </motion.div>
                                ),
                            )}
                        </motion.div>
                    </div>
                </div>
            </section>

            {/* ── MOBILE REPORT TYPES (visible only on small screens) ── */}
            <section className="md:hidden py-16 bg-white">
                <div className="px-6">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-40px" }}
                        variants={staggerContainer}
                        className="space-y-3"
                    >
                        {reportTypes.map(
                            ({ icon: Icon, labelKey, descKey, type }) => (
                                <motion.div key={type} variants={slideUp}>
                                    <Link
                                        href={localePath(
                                            `/get-report?type=${type}`,
                                        )}
                                        className="group flex items-center gap-4 bg-white border border-slate-200/60 p-5 transition-all duration-300 hover:border-brand-secondary/30 active:scale-[0.98]"
                                    >
                                        <Icon
                                            size={22}
                                            weight="duotone"
                                            className="text-brand-primary shrink-0 group-hover:text-brand-secondary transition-colors"
                                        />
                                        <div className="min-w-0">
                                            <h3 className="font-semibold text-brand-primary text-sm tracking-tight">
                                                {t(labelKey)}
                                            </h3>
                                            <p className="text-xs text-slate-400 leading-relaxed mt-0.5">
                                                {t(descKey)}
                                            </p>
                                        </div>
                                        <ArrowRight
                                            size={14}
                                            weight="bold"
                                            className="text-slate-300 shrink-0 ml-auto group-hover:text-brand-secondary transition-colors"
                                        />
                                    </Link>
                                </motion.div>
                            ),
                        )}
                    </motion.div>
                </div>
            </section>

            {/* ── HOW IT WORKS — Vertical stepped timeline ── */}
            <section id="how-it-works" className="py-24 md:py-32 bg-white">
                <div className="mx-auto max-w-350 px-6 md:px-10 lg:px-16">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-80px" }}
                        variants={staggerContainer}
                        className="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] gap-12 lg:gap-20"
                    >
                        {/* Left — Section header, pinned feel */}
                        <motion.div
                            variants={slideRight}
                            className="lg:sticky lg:top-32 lg:self-start"
                        >
                            <p className="text-xs font-semibold tracking-[0.2em] uppercase text-brand-secondary mb-4">
                                {t("landing_how_title")}
                            </p>
                            <h2 className="text-3xl md:text-4xl font-bold tracking-tighter leading-none text-brand-primary mb-4">
                                {t("landing_how_desc")}
                            </h2>
                            <div className="w-12 h-0.5 bg-brand-secondary/40" />
                        </motion.div>

                        {/* Right — Steps */}
                        <motion.div
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true, margin: "-60px" }}
                            variants={staggerContainer}
                            className="space-y-0"
                        >
                            {steps.map(
                                ({ num, icon: Icon, titleKey, descKey }, i) => (
                                    <motion.div
                                        key={num}
                                        variants={slideLeft}
                                        className={`relative flex gap-6 md:gap-8 pb-12 ${
                                            i < steps.length - 1
                                                ? "border-l border-slate-200 ml-4 pl-8"
                                                : "ml-4 pl-8"
                                        }`}
                                    >
                                        {/* Step number dot */}
                                        <div className="absolute left-0 -translate-x-1/2 top-0 w-9 h-9 bg-brand-primary text-white flex items-center justify-center text-xs font-mono font-bold shrink-0">
                                            {num}
                                        </div>

                                        <div className="pt-0.5">
                                            <Icon
                                                size={20}
                                                weight="duotone"
                                                className="text-brand-secondary mb-3"
                                            />
                                            <h3 className="font-bold text-brand-primary tracking-tight mb-2">
                                                {t(titleKey)}
                                            </h3>
                                            <p className="text-sm text-slate-500 leading-relaxed max-w-[50ch]">
                                                {t(descKey)}
                                            </p>
                                        </div>
                                    </motion.div>
                                ),
                            )}
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* ── FEATURES — Zig-zag pairs ── */}
            <section className="py-24 md:py-32 bg-[#fafaf9]">
                <div className="mx-auto max-w-350 px-6 md:px-10 lg:px-16 space-y-20 md:space-y-28">
                    {/* Section label */}
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={slideUp}
                    >
                        <p className="text-xs font-semibold tracking-[0.2em] uppercase text-brand-secondary mb-4">
                            {t("landing_features_title")}
                        </p>
                        <h2 className="text-3xl md:text-4xl font-bold tracking-tighter leading-none text-brand-primary max-w-lg">
                            {t("landing_features_desc")}
                        </h2>
                    </motion.div>

                    {/* Row 1: left text, right visual block */}
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={staggerContainer}
                        className="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-start"
                    >
                        <motion.div variants={slideRight} className="space-y-8">
                            {[
                                {
                                    icon: ChartLineUp,
                                    titleKey: "landing_feat1_title",
                                    descKey: "landing_feat1_desc",
                                },
                                {
                                    icon: MapPin,
                                    titleKey: "landing_feat2_title",
                                    descKey: "landing_feat2_desc",
                                },
                                {
                                    icon: ShieldCheck,
                                    titleKey: "landing_feat3_title",
                                    descKey: "landing_feat3_desc",
                                },
                            ].map(({ icon: Icon, titleKey, descKey }) => (
                                <div key={titleKey} className="flex gap-4">
                                    <div className="w-9 h-9 bg-brand-secondary/8 flex items-center justify-center shrink-0 mt-0.5">
                                        <Icon
                                            size={18}
                                            weight="duotone"
                                            className="text-brand-secondary"
                                        />
                                    </div>
                                    <div>
                                        <h3 className="font-semibold text-brand-primary text-sm tracking-tight mb-1">
                                            {t(titleKey)}
                                        </h3>
                                        <p className="text-sm text-slate-500 leading-relaxed max-w-[50ch]">
                                            {t(descKey)}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </motion.div>

                        <motion.div
                            variants={slideLeft}
                            className="bg-white border border-slate-200/60 p-8 md:p-10"
                        >
                            <div className="space-y-4">
                                {[
                                    {
                                        label: t("landing_stat_market"),
                                        value: "87.3",
                                        suffix: "/100",
                                    },
                                    {
                                        label: t("landing_stat_risk"),
                                        value: "Low",
                                    },
                                    {
                                        label: t("landing_stat_area"),
                                        value: "9.1",
                                        suffix: "/10",
                                    },
                                ].map(({ label, value, suffix }) => (
                                    <div
                                        key={label}
                                        className="flex items-center justify-between py-3 border-b border-slate-100 last:border-0"
                                    >
                                        <span className="text-sm text-slate-500">
                                            {label}
                                        </span>
                                        <span className="font-mono font-bold text-brand-primary text-sm tabular-nums">
                                            {value}
                                            {suffix && (
                                                <span className="text-slate-300 font-normal">
                                                    {suffix}
                                                </span>
                                            )}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </motion.div>
                    </motion.div>

                    {/* Row 2: reversed — visual left, text right */}
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={staggerContainer}
                        className="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-start"
                    >
                        <motion.div
                            variants={slideRight}
                            className="bg-white border border-slate-200/60 p-8 md:p-10 order-2 md:order-1"
                        >
                            <div className="grid grid-cols-2 gap-6">
                                {[
                                    {
                                        label: t("landing_stat_score"),
                                        value: "8.4",
                                    },
                                    {
                                        label: t("landing_stat_delivery"),
                                        value: t("landing_stat_fast"),
                                    },
                                    {
                                        label: t("landing_stat_sections"),
                                        value: "12+",
                                    },
                                    {
                                        label: t("landing_stat_format"),
                                        value: "PDF",
                                    },
                                ].map(({ label, value }) => (
                                    <div key={label}>
                                        <p className="text-xs text-slate-400 mb-1">
                                            {label}
                                        </p>
                                        <p className="font-mono font-bold text-brand-primary text-lg tabular-nums">
                                            {value}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </motion.div>

                        <motion.div
                            variants={slideLeft}
                            className="space-y-8 order-1 md:order-2"
                        >
                            {[
                                {
                                    icon: Pulse,
                                    titleKey: "landing_feat4_title",
                                    descKey: "landing_feat4_desc",
                                },
                                {
                                    icon: Scales,
                                    titleKey: "landing_feat5_title",
                                    descKey: "landing_feat5_desc",
                                },
                                {
                                    icon: FilePdf,
                                    titleKey: "landing_feat6_title",
                                    descKey: "landing_feat6_desc",
                                },
                            ].map(({ icon: Icon, titleKey, descKey }) => (
                                <div key={titleKey} className="flex gap-4">
                                    <div className="w-9 h-9 bg-brand-secondary/8 flex items-center justify-center shrink-0 mt-0.5">
                                        <Icon
                                            size={18}
                                            weight="duotone"
                                            className="text-brand-secondary"
                                        />
                                    </div>
                                    <div>
                                        <h3 className="font-semibold text-brand-primary text-sm tracking-tight mb-1">
                                            {t(titleKey)}
                                        </h3>
                                        <p className="text-sm text-slate-500 leading-relaxed max-w-[50ch]">
                                            {t(descKey)}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* ── BOTTOM CTA — Clean, warm section ── */}
            <section className="py-24 md:py-32 bg-white">
                <div className="mx-auto max-w-350 px-6 md:px-10 lg:px-16">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={staggerContainer}
                        className="bg-brand-primary p-10 md:p-16 flex flex-col md:flex-row items-start md:items-center justify-between gap-8"
                    >
                        <motion.div variants={slideRight} className="max-w-lg">
                            <h2 className="text-2xl md:text-3xl font-bold tracking-tighter leading-tight text-white mb-3">
                                {t("landing_cta_title")}
                            </h2>
                            <p className="text-sm text-white/50 leading-relaxed max-w-[50ch]">
                                {t("landing_cta_desc")}
                            </p>
                        </motion.div>

                        <motion.div variants={slideLeft}>
                            <Link
                                href={localePath("/get-report")}
                                className="group inline-flex items-center gap-2.5 bg-brand-secondary text-white font-medium px-8 py-3.5 text-sm transition-all duration-200 hover:scale-[0.98] active:scale-[0.96]"
                            >
                                {t("landing_cta_primary")}
                                <ArrowRight
                                    size={15}
                                    weight="bold"
                                    className="transition-transform duration-200 group-hover:translate-x-0.5"
                                />
                            </Link>
                        </motion.div>
                    </motion.div>
                </div>
            </section>
        </PublicLayout>
    );
}
