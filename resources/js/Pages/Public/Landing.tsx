import { Head, Link } from "@inertiajs/react";
import {
    ArrowRight,
    ChartLineUp,
    ShieldCheck,
    Lightning,
    MagnifyingGlass,
    EnvelopeSimple,
    FilePdf,
    CheckCircle,
    House,
    Storefront,
    Key,
    ShoppingCart,
    Star,
} from "@phosphor-icons/react";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { motion } from "framer-motion";

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.1, ease: "easeOut" as const },
    }),
};

const scaleIn = {
    hidden: { opacity: 0, scale: 0.9 },
    visible: (i: number) => ({
        opacity: 1,
        scale: 1,
        transition: {
            duration: 0.45,
            delay: i * 0.08,
            ease: "easeOut" as const,
        },
    }),
};

export default function Landing() {
    const { t, localePath } = useTranslation();

    return (
        <PublicLayout>
            <Head title={t("landing_meta_title")} />

            {/* ── HERO ── */}
            <section className="relative overflow-hidden bg-linear-to-b from-brand-primary to-[#252338] text-white">
                {/* Subtle grid pattern */}
                <div
                    className="absolute inset-0 opacity-[0.04]"
                    style={{
                        backgroundImage:
                            "linear-gradient(rgba(255,255,255,.3) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.3) 1px, transparent 1px)",
                        backgroundSize: "48px 48px",
                    }}
                />

                <div className="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-24 md:py-32 text-center">
                    <motion.div
                        initial="hidden"
                        animate="visible"
                        custom={0}
                        variants={fadeUp}
                        className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/10 px-4 py-1.5 mb-6 text-xs font-semibold tracking-wide uppercase"
                    >
                        <Lightning
                            size={14}
                            weight="fill"
                            className="text-brand-secondary"
                        />
                        {t("landing_badge")}
                    </motion.div>

                    <motion.h1
                        initial="hidden"
                        animate="visible"
                        custom={1}
                        variants={fadeUp}
                        className="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-[1.1] mb-6"
                    >
                        {t("landing_hero_title_1")}
                        <br />
                        <span className="text-brand-secondary">
                            {t("landing_hero_title_2")}
                        </span>
                    </motion.h1>

                    <motion.p
                        initial="hidden"
                        animate="visible"
                        custom={2}
                        variants={fadeUp}
                        className="max-w-2xl mx-auto text-lg text-white/70 mb-10 leading-relaxed"
                    >
                        {t("landing_hero_desc")}
                    </motion.p>

                    <motion.div
                        initial="hidden"
                        animate="visible"
                        custom={3}
                        variants={fadeUp}
                        className="flex flex-col sm:flex-row items-center justify-center gap-4"
                    >
                        <Link
                            href={localePath("/get-report")}
                            className="inline-flex items-center gap-2 bg-brand-secondary hover:bg-brand-secondary/90 text-white font-semibold px-7 py-3 text-sm transition-colors"
                        >
                            {t("landing_cta_primary")}
                            <ArrowRight size={16} weight="bold" />
                        </Link>
                        <a
                            href="#how-it-works"
                            className="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 border border-white/15 text-white font-semibold px-7 py-3 text-sm transition-colors"
                        >
                            {t("landing_cta_secondary")}
                        </a>
                    </motion.div>

                    {/* Trust indicators */}
                    <motion.div
                        initial="hidden"
                        animate="visible"
                        custom={4}
                        variants={fadeUp}
                        className="mt-14 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-xs text-white/50"
                    >
                        {[
                            t("landing_trust_1"),
                            t("landing_trust_2"),
                            t("landing_trust_3"),
                        ].map((item, i) => (
                            <span key={i} className="flex items-center gap-1.5">
                                <CheckCircle
                                    size={14}
                                    weight="fill"
                                    className="text-brand-secondary"
                                />
                                {item}
                            </span>
                        ))}
                    </motion.div>
                </div>
            </section>

            {/* ── REPORT TYPES ── */}
            <section className="py-20 md:py-28 bg-white">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        custom={0}
                        variants={fadeUp}
                        className="text-center mb-14"
                    >
                        <h2 className="text-3xl md:text-4xl font-bold text-brand-primary tracking-tight mb-3">
                            {t("landing_types_title")}
                        </h2>
                        <p className="text-brand-neutral max-w-xl mx-auto">
                            {t("landing_types_desc")}
                        </p>
                    </motion.div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        {[
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
                        ].map(({ icon: Icon, labelKey, descKey, type }, i) => (
                            <motion.div
                                key={type}
                                initial="hidden"
                                whileInView="visible"
                                viewport={{ once: true, margin: "-40px" }}
                                custom={i}
                                variants={scaleIn}
                            >
                                <Link
                                    href={localePath(
                                        `/get-report?type=${type}`,
                                    )}
                                    className="group block border border-gray-200 p-6 hover:border-brand-tertiary/40 hover:shadow-lg hover:shadow-brand-tertiary/5 transition-all duration-300"
                                >
                                    <div className="w-11 h-11 bg-brand-tertiary/10 flex items-center justify-center mb-4 group-hover:bg-brand-tertiary/15 transition-colors">
                                        <Icon
                                            size={22}
                                            weight="duotone"
                                            className="text-brand-tertiary"
                                        />
                                    </div>
                                    <h3 className="font-semibold text-brand-primary text-sm mb-1">
                                        {t(labelKey)}
                                    </h3>
                                    <p className="text-xs text-brand-neutral leading-relaxed">
                                        {t(descKey)}
                                    </p>
                                    <span className="inline-flex items-center gap-1 text-xs font-semibold text-brand-tertiary mt-4 group-hover:gap-2 transition-all">
                                        {t("landing_start_report")}
                                        <ArrowRight size={12} weight="bold" />
                                    </span>
                                </Link>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── HOW IT WORKS ── */}
            <section id="how-it-works" className="py-20 md:py-28 bg-gray-50">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        custom={0}
                        variants={fadeUp}
                        className="text-center mb-14"
                    >
                        <h2 className="text-3xl md:text-4xl font-bold text-brand-primary tracking-tight mb-3">
                            {t("landing_how_title")}
                        </h2>
                        <p className="text-brand-neutral max-w-xl mx-auto">
                            {t("landing_how_desc")}
                        </p>
                    </motion.div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {[
                            {
                                icon: MagnifyingGlass,
                                numKey: "1",
                                titleKey: "landing_step1_title",
                                descKey: "landing_step1_desc",
                            },
                            {
                                icon: EnvelopeSimple,
                                numKey: "2",
                                titleKey: "landing_step2_title",
                                descKey: "landing_step2_desc",
                            },
                            {
                                icon: FilePdf,
                                numKey: "3",
                                titleKey: "landing_step3_title",
                                descKey: "landing_step3_desc",
                            },
                        ].map(
                            ({ icon: Icon, numKey, titleKey, descKey }, i) => (
                                <motion.div
                                    key={numKey}
                                    initial="hidden"
                                    whileInView="visible"
                                    viewport={{ once: true, margin: "-40px" }}
                                    custom={i}
                                    variants={fadeUp}
                                    className="relative bg-white border border-gray-200 p-8 text-center"
                                >
                                    <div className="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 bg-brand-secondary flex items-center justify-center text-white text-xs font-bold">
                                        {numKey}
                                    </div>
                                    <div className="w-14 h-14 mx-auto bg-brand-primary/5 flex items-center justify-center mb-5 mt-2">
                                        <Icon
                                            size={26}
                                            weight="duotone"
                                            className="text-brand-primary"
                                        />
                                    </div>
                                    <h3 className="font-bold text-brand-primary mb-2">
                                        {t(titleKey)}
                                    </h3>
                                    <p className="text-sm text-brand-neutral leading-relaxed">
                                        {t(descKey)}
                                    </p>
                                </motion.div>
                            ),
                        )}
                    </div>
                </div>
            </section>

            {/* ── FEATURES / WHAT'S INCLUDED ── */}
            <section className="py-20 md:py-28 bg-white">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        custom={0}
                        variants={fadeUp}
                        className="text-center mb-14"
                    >
                        <h2 className="text-3xl md:text-4xl font-bold text-brand-primary tracking-tight mb-3">
                            {t("landing_features_title")}
                        </h2>
                        <p className="text-brand-neutral max-w-xl mx-auto">
                            {t("landing_features_desc")}
                        </p>
                    </motion.div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {[
                            {
                                icon: ChartLineUp,
                                titleKey: "landing_feat1_title",
                                descKey: "landing_feat1_desc",
                            },
                            {
                                icon: MagnifyingGlass,
                                titleKey: "landing_feat2_title",
                                descKey: "landing_feat2_desc",
                            },
                            {
                                icon: ShieldCheck,
                                titleKey: "landing_feat3_title",
                                descKey: "landing_feat3_desc",
                            },
                            {
                                icon: Lightning,
                                titleKey: "landing_feat4_title",
                                descKey: "landing_feat4_desc",
                            },
                            {
                                icon: Star,
                                titleKey: "landing_feat5_title",
                                descKey: "landing_feat5_desc",
                            },
                            {
                                icon: FilePdf,
                                titleKey: "landing_feat6_title",
                                descKey: "landing_feat6_desc",
                            },
                        ].map(({ icon: Icon, titleKey, descKey }, i) => (
                            <motion.div
                                key={titleKey}
                                initial="hidden"
                                whileInView="visible"
                                viewport={{ once: true, margin: "-40px" }}
                                custom={i}
                                variants={scaleIn}
                                className="flex gap-4 p-5 border border-gray-100 hover:border-gray-200 transition-colors"
                            >
                                <div className="w-10 h-10 bg-brand-secondary/10 flex items-center justify-center shrink-0">
                                    <Icon
                                        size={20}
                                        weight="duotone"
                                        className="text-brand-secondary"
                                    />
                                </div>
                                <div>
                                    <h3 className="font-semibold text-brand-primary text-sm mb-1">
                                        {t(titleKey)}
                                    </h3>
                                    <p className="text-xs text-brand-neutral leading-relaxed">
                                        {t(descKey)}
                                    </p>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── BOTTOM CTA ── */}
            <section className="py-20 md:py-28 bg-brand-primary relative overflow-hidden">
                <div
                    className="absolute inset-0 opacity-[0.04]"
                    style={{
                        backgroundImage:
                            "linear-gradient(rgba(255,255,255,.3) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.3) 1px, transparent 1px)",
                        backgroundSize: "48px 48px",
                    }}
                />
                <div className="relative mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 text-center">
                    <motion.div
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        custom={0}
                        variants={fadeUp}
                    >
                        <h2 className="text-3xl md:text-4xl font-bold text-white tracking-tight mb-4">
                            {t("landing_cta_title")}
                        </h2>
                        <p className="text-white/60 mb-8 max-w-lg mx-auto leading-relaxed">
                            {t("landing_cta_desc")}
                        </p>
                        <Link
                            href={localePath("/get-report")}
                            className="inline-flex items-center gap-2 bg-brand-secondary hover:bg-brand-secondary/90 text-white font-semibold px-8 py-3.5 text-sm transition-colors"
                        >
                            {t("landing_cta_primary")}
                            <ArrowRight size={16} weight="bold" />
                        </Link>
                    </motion.div>
                </div>
            </section>
        </PublicLayout>
    );
}
