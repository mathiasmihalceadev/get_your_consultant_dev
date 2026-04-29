import { Head, Link } from "@inertiajs/react";
import {
    ArrowRight,
    ChartLineUp,
    ShieldCheck,
    MagnifyingGlass,
    EnvelopeSimple,
    FilePdf,
    House,
    Key,
    Scales,
    MapPin,
    Pulse,
    CheckCircle,
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
            detailKeys: [
                "landing_rental_detail_1",
                "landing_rental_detail_2",
                "landing_rental_detail_3",
            ],
        },
        {
            icon: House,
            labelKey: "type_buying_living",
            descKey: "buying_living_desc",
            type: "buying_living",
            detailKeys: [
                "landing_buying_detail_1",
                "landing_buying_detail_2",
                "landing_buying_detail_3",
            ],
        },
    ];

    const comparePoints = [
        {
            icon: Pulse,
            labelKey: "landing_compare_point_1_label",
            humanKey: "landing_compare_point_1_human",
            reportKey: "landing_compare_point_1_report",
        },
        {
            icon: Scales,
            labelKey: "landing_compare_point_2_label",
            humanKey: "landing_compare_point_2_human",
            reportKey: "landing_compare_point_2_report",
        },
        {
            icon: CheckCircle,
            labelKey: "landing_compare_point_3_label",
            humanKey: "landing_compare_point_3_human",
            reportKey: "landing_compare_point_3_report",
        },
        {
            icon: ChartLineUp,
            labelKey: "landing_compare_point_4_label",
            humanKey: "landing_compare_point_4_human",
            reportKey: "landing_compare_point_4_report",
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

            {/* Hero */}
            <section className="bg-white py-14 md:py-18">
                <div className="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        animate="visible"
                        variants={staggerContainer}
                        className="text-center max-w-3xl mx-auto"
                    >
                        <motion.p
                            variants={slideUp}
                            className="text-xs font-semibold tracking-[0.18em] uppercase text-brand-secondary mb-4"
                        >
                            {t("landing_badge")}
                        </motion.p>

                        <motion.h1
                            variants={slideUp}
                            className="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tighter leading-[0.95] text-brand-primary mb-4"
                        >
                            {t("landing_hero_title_1")}
                            <br />
                            <span className="text-brand-secondary">
                                {t("landing_hero_title_2")}
                            </span>
                        </motion.h1>

                        <motion.p
                            variants={slideUp}
                            className="text-base text-slate-500 leading-relaxed max-w-2xl mx-auto mb-7"
                        >
                            {t("landing_hero_desc")}
                        </motion.p>

                        <motion.div
                            variants={slideUp}
                            className="flex flex-wrap items-center justify-center gap-3"
                        >
                            <Link
                                href={localePath("/get-report")}
                                className="group inline-flex items-center gap-2.5 bg-brand-primary text-white font-medium px-6 py-3 text-sm transition-all duration-200 hover:scale-[0.98] active:scale-[0.96]"
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
                                className="inline-flex items-center gap-2 text-sm font-medium text-brand-neutral hover:text-brand-primary transition-colors duration-200 border border-gray-200 px-5 py-3"
                            >
                                {t("landing_cta_secondary")}
                            </a>
                        </motion.div>

                        <motion.div
                            variants={slideUp}
                            className="mt-6 flex flex-wrap items-center justify-center gap-4 text-xs text-slate-400"
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
                                    <span className="w-1.5 h-1.5 rounded-full bg-brand-secondary" />
                                    {item}
                                </span>
                            ))}
                        </motion.div>
                    </motion.div>

                    <motion.div
                        initial="hidden"
                        animate="visible"
                        variants={staggerContainer}
                        className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-10 max-w-5xl mx-auto"
                    >
                        {reportTypes.map(
                            ({
                                icon: Icon,
                                labelKey,
                                descKey,
                                type,
                                detailKeys,
                            }) => (
                                <motion.div key={type} variants={scaleIn}>
                                    <Link
                                        href={localePath(
                                            `/get-report?type=${type}`,
                                        )}
                                        className="group block bg-white border border-slate-200 p-6 md:p-7 h-full transition-all duration-300 hover:border-brand-secondary/40 hover:shadow-[0_18px_40px_-24px_rgba(52,48,106,0.3)]"
                                    >
                                        <div className="mb-5">
                                            <div className="w-14 h-14 bg-brand-primary/5 flex items-center justify-center">
                                                <Icon
                                                    size={32}
                                                    weight="duotone"
                                                    className="text-brand-primary group-hover:text-brand-secondary transition-colors"
                                                />
                                            </div>
                                        </div>

                                        <h3 className="font-bold text-brand-primary text-lg tracking-tight mb-2">
                                            {t(labelKey)}
                                        </h3>
                                        <p className="text-sm text-slate-500 leading-relaxed mb-4">
                                            {t(descKey)}
                                        </p>

                                        <div className="space-y-2.5 mb-5">
                                            {detailKeys.map((key) => (
                                                <div
                                                    key={key}
                                                    className="flex items-start gap-2"
                                                >
                                                    <CheckCircle
                                                        size={16}
                                                        weight="fill"
                                                        className="text-brand-secondary shrink-0 mt-0.5"
                                                    />
                                                    <span className="text-sm text-brand-neutral">
                                                        {t(key)}
                                                    </span>
                                                </div>
                                            ))}
                                        </div>

                                        <div className="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-secondary">
                                            {t("landing_start_report")}
                                            <ArrowRight
                                                size={13}
                                                weight="bold"
                                            />
                                        </div>
                                    </Link>
                                </motion.div>
                            ),
                        )}
                    </motion.div>
                </div>
            </section>

            {/* ── HOW IT WORKS — Vertical stepped timeline ── */}
            <section
                id="how-it-works"
                className="py-14 md:py-18 bg-white border-t border-gray-100"
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-80px" }}
                        variants={staggerContainer}
                        className="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] gap-8 lg:gap-14"
                    >
                        {/* Left — Section header, pinned feel */}
                        <motion.div
                            variants={slideRight}
                            className="lg:sticky lg:top-28 lg:self-start"
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
                                        className={`relative flex gap-5 md:gap-7 pb-8 ${
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
            <section
                id="report-content"
                className="py-14 md:py-18 bg-[#fafaf9]"
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-12 md:space-y-16">
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
                                    <div className="w-11 h-11 bg-brand-secondary/8 flex items-center justify-center shrink-0 mt-0.5">
                                        <Icon
                                            size={22}
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
                            className="bg-white border border-slate-200/60 p-6 md:p-8"
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
                            className="bg-white border border-slate-200/60 p-6 md:p-8 order-2 md:order-1"
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
                                    <div className="w-11 h-11 bg-brand-secondary/8 flex items-center justify-center shrink-0 mt-0.5">
                                        <Icon
                                            size={22}
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

            {/* Consultant vs Report */}
            <section
                id="consultant-vs-report"
                className="py-14 md:py-18 bg-white border-t border-gray-100"
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={staggerContainer}
                        className="grid grid-cols-1 lg:grid-cols-[0.9fr_1.1fr] border border-slate-200 bg-white"
                    >
                        <motion.div
                            variants={slideRight}
                            className="border-b border-slate-200 bg-brand-primary px-6 py-8 md:px-8 md:py-10 lg:border-b-0 lg:border-r"
                        >
                            <p className="text-xs font-semibold tracking-[0.2em] uppercase text-brand-secondary mb-3">
                                {t("landing_compare_badge")}
                            </p>
                            <h2 className="text-3xl md:text-4xl font-bold tracking-tighter leading-none text-white mb-4">
                                {t("landing_compare_title")}
                            </h2>
                            <p className="text-sm md:text-base leading-relaxed text-white/72 max-w-md">
                                {t("landing_compare_desc")}
                            </p>

                            <div className="mt-8 space-y-3 border-t border-white/12 pt-6">
                                {comparePoints.map(
                                    ({ icon: Icon, labelKey }, index) => (
                                        <div
                                            key={labelKey}
                                            className="flex items-start gap-3 border-b border-white/12 pb-3 last:border-0 last:pb-0"
                                        >
                                            <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-white/10 text-brand-secondary">
                                                <Icon
                                                    size={18}
                                                    weight="duotone"
                                                />
                                            </div>
                                            <div>
                                                <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45 mb-1">
                                                    {String(index + 1).padStart(
                                                        2,
                                                        "0",
                                                    )}
                                                </p>
                                                <p className="text-sm font-semibold leading-snug text-white">
                                                    {t(labelKey)}
                                                </p>
                                            </div>
                                        </div>
                                    ),
                                )}
                            </div>
                        </motion.div>

                        <motion.div
                            variants={slideLeft}
                            className="px-4 py-6 md:px-8 md:py-8"
                        >
                            <div className="mb-4 hidden md:grid md:grid-cols-[0.72fr_1fr_1fr] md:gap-0">
                                <span className="border-b border-slate-200 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                                    {t("landing_compare_badge")}
                                </span>
                                <span className="border-b border-l border-slate-200 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 text-center bg-slate-50">
                                    {t("landing_compare_human_title")}
                                </span>
                                <span className="border-b border-l border-slate-200 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-brand-primary text-center bg-brand-secondary/10">
                                    {t("landing_compare_report_title")}
                                </span>
                            </div>

                            <div>
                                {comparePoints.map(
                                    (
                                        {
                                            icon: Icon,
                                            labelKey,
                                            humanKey,
                                            reportKey,
                                        },
                                        index,
                                    ) => (
                                        <div
                                            key={labelKey}
                                            className="grid border-b border-slate-200 last:border-0 md:grid-cols-[0.72fr_1fr_1fr] md:items-stretch"
                                        >
                                            <div className="flex h-full items-center gap-3 px-4 py-4 bg-white">
                                                <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-brand-primary text-white">
                                                    <Icon
                                                        size={18}
                                                        weight="duotone"
                                                    />
                                                </div>
                                                <div>
                                                    <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400 mb-1 md:hidden">
                                                        {String(
                                                            index + 1,
                                                        ).padStart(2, "0")}
                                                    </p>
                                                    <p className="text-sm font-semibold text-brand-primary leading-snug">
                                                        {t(labelKey)}
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="flex min-h-28 h-full flex-col justify-between border-t border-slate-200 px-4 py-4 md:min-h-0 md:border-t-0 md:border-l bg-white">
                                                <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400 md:hidden">
                                                    {t(
                                                        "landing_compare_human_title",
                                                    )}
                                                </p>
                                                <div className="mt-4 flex flex-1 flex-col justify-end md:mt-0 md:justify-center">
                                                    <p className="text-sm font-medium leading-relaxed text-brand-primary">
                                                        {t(humanKey)}
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="flex min-h-28 h-full flex-col justify-between border-t border-slate-200 px-4 py-4 md:min-h-0 md:border-t-0 md:border-l bg-brand-secondary/10">
                                                <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/60 md:hidden">
                                                    {t(
                                                        "landing_compare_report_title",
                                                    )}
                                                </p>
                                                <div className="mt-4 flex flex-1 flex-col justify-end md:mt-0 md:justify-center">
                                                    <p className="text-sm font-semibold leading-relaxed text-brand-primary">
                                                        {t(reportKey)}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    ),
                                )}
                            </div>

                            <div className="mt-6 flex justify-start md:justify-end">
                                <Link
                                    href={localePath("/get-report")}
                                    className="inline-flex items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92"
                                >
                                    {t("landing_cta_primary")}
                                    <ArrowRight size={14} weight="bold" />
                                </Link>
                            </div>
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* Pricing */}
            <section
                id="pricing"
                className="py-14 md:py-18 bg-[#fafaf9] border-t border-gray-100"
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={staggerContainer}
                        className="text-center max-w-3xl mx-auto mb-8"
                    >
                        <p className="text-xs font-semibold tracking-[0.2em] uppercase text-brand-secondary mb-3">
                            {t("landing_pricing_badge")}
                        </p>
                        <h2 className="text-3xl md:text-4xl font-bold tracking-tighter leading-none text-brand-primary mb-3">
                            {t("landing_pricing_title")}
                        </h2>
                        <p className="text-sm md:text-base text-slate-500 leading-relaxed">
                            {t("landing_pricing_desc")}
                        </p>
                    </motion.div>

                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={staggerContainer}
                        className="grid grid-cols-1 md:grid-cols-2 gap-5"
                    >
                        <motion.div
                            variants={scaleIn}
                            className="bg-white border border-slate-200 p-6 md:p-7"
                        >
                            <p className="text-sm font-semibold text-brand-primary mb-2">
                                {t("type_rental_living")}
                            </p>
                            <p className="text-4xl font-bold text-brand-primary tracking-tight mb-2">
                                17 EUR
                            </p>
                            <p className="text-xs text-slate-500 mb-5">
                                {t("landing_price_vat_included")}
                            </p>
                            <div className="space-y-2.5 mb-6">
                                {[1, 2, 3, 4].map((item) => (
                                    <div
                                        key={item}
                                        className="flex items-start gap-2"
                                    >
                                        <CheckCircle
                                            size={16}
                                            weight="fill"
                                            className="text-brand-secondary shrink-0 mt-0.5"
                                        />
                                        <p className="text-sm text-brand-neutral">
                                            {t(
                                                `landing_pricing_rental_feature_${item}`,
                                            )}
                                        </p>
                                    </div>
                                ))}
                            </div>
                            <Link
                                href={localePath(
                                    "/get-report?type=rental_living",
                                )}
                                className="inline-flex items-center gap-2 text-sm font-semibold bg-brand-primary text-white px-4 py-2.5 hover:bg-brand-primary/90 transition-colors"
                            >
                                {t("landing_pricing_cta")}
                                <ArrowRight size={14} weight="bold" />
                            </Link>
                        </motion.div>

                        <motion.div
                            variants={scaleIn}
                            className="bg-white border border-brand-secondary/40 p-6 md:p-7"
                        >
                            <p className="text-sm font-semibold text-brand-primary mb-2">
                                {t("type_buying_living")}
                            </p>
                            <p className="text-4xl font-bold text-brand-primary tracking-tight mb-2">
                                30 EUR
                            </p>
                            <p className="text-xs text-slate-500 mb-5">
                                {t("landing_price_vat_included")}
                            </p>
                            <div className="space-y-2.5 mb-6">
                                {[1, 2, 3, 4].map((item) => (
                                    <div
                                        key={item}
                                        className="flex items-start gap-2"
                                    >
                                        <CheckCircle
                                            size={16}
                                            weight="fill"
                                            className="text-brand-secondary shrink-0 mt-0.5"
                                        />
                                        <p className="text-sm text-brand-neutral">
                                            {t(
                                                `landing_pricing_buying_feature_${item}`,
                                            )}
                                        </p>
                                    </div>
                                ))}
                            </div>
                            <Link
                                href={localePath(
                                    "/get-report?type=buying_living",
                                )}
                                className="inline-flex items-center gap-2 text-sm font-semibold bg-brand-secondary text-white px-4 py-2.5 hover:bg-brand-secondary/90 transition-colors"
                            >
                                {t("landing_pricing_cta")}
                                <ArrowRight size={14} weight="bold" />
                            </Link>
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* ── BOTTOM CTA — Clean, warm section ── */}
            <section className="py-14 md:py-18 bg-white border-t border-gray-100">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={staggerContainer}
                        className="bg-brand-primary p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6"
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

            <section
                id="contact"
                className="py-12 bg-[#fafaf9] border-t border-gray-100"
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 text-center">
                    <h2 className="text-2xl md:text-3xl font-bold tracking-tight text-brand-primary mb-3">
                        {t("landing_contact_title")}
                    </h2>
                    <p className="text-sm md:text-base text-slate-500 max-w-2xl mx-auto mb-4">
                        {t("landing_contact_desc")}
                    </p>
                    <a
                        href="mailto:contact@getyourconsultant.com"
                        className="inline-flex items-center gap-2 text-sm font-semibold text-brand-primary hover:text-brand-secondary transition-colors"
                    >
                        contact@getyourconsultant.com
                    </a>
                </div>
            </section>

            <section
                id="privacy-policy"
                className="sr-only"
                aria-hidden="true"
            />
            <section
                id="terms-and-conditions"
                className="sr-only"
                aria-hidden="true"
            />
            <section
                id="cookie-policy"
                className="sr-only"
                aria-hidden="true"
            />
        </PublicLayout>
    );
}
