import { type ComponentType, type FormEvent, useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { ArrowRight, CheckCircle } from "@phosphor-icons/react";
import { motion, useReducedMotion, type Variants } from "framer-motion";
import {
    type IconProps,
    ClipboardCheck,
    ClipboardList,
    Database,
    DocumentText,
    GraphNewUp,
    HandMoney,
    HomeSmile,
    KeyMinimalistic,
    PieChart,
    RoundedMagnifier,
    ShieldCheck,
    ShieldWarning,
    TagPrice,
    WalletMoney,
    MapPointSearch,
} from "@solar-icons/react";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { ReportType } from "@/types";

type LandingIcon = ComponentType<IconProps>;

const typeOptions: {
    type: ReportType;
    icon: LandingIcon;
    iconWeight: IconProps["weight"];
    labelKey: string;
    priceKey: string;
}[] = [
    {
        type: "buying_living",
        icon: HomeSmile,
        iconWeight: "BoldDuotone",
        labelKey: "buying",
        priceKey: "landing_pricing_buying_price",
    },
    {
        type: "rental_living",
        icon: KeyMinimalistic,
        iconWeight: "BoldDuotone",
        labelKey: "rental",
        priceKey: "landing_pricing_rental_price",
    },
];

const exampleFeatures: {
    key: string;
    icon: LandingIcon;
    weight: IconProps["weight"];
}[] = [
    {
        key: "landing_example_feature_1",
        icon: ClipboardCheck,
        weight: "BoldDuotone",
    },
    {
        key: "landing_example_feature_2",
        icon: TagPrice,
        weight: "BoldDuotone",
    },
    {
        key: "landing_example_feature_3",
        icon: MapPointSearch,
        weight: "BoldDuotone",
    },
    {
        key: "landing_example_feature_4",
        icon: GraphNewUp,
        weight: "BoldDuotone",
    },
    {
        key: "landing_example_feature_5",
        icon: ShieldWarning,
        weight: "BoldDuotone",
    },
];

const problemItems: {
    key: string;
    icon: LandingIcon;
    weight: IconProps["weight"];
}[] = [
    {
        key: "landing_problem_item_1",
        icon: TagPrice,
        weight: "BoldDuotone",
    },
    {
        key: "landing_problem_item_2",
        icon: ShieldWarning,
        weight: "BoldDuotone",
    },
    {
        key: "landing_problem_item_3",
        icon: WalletMoney,
        weight: "BoldDuotone",
    },
];

const solutionItems: {
    key: string;
    icon: LandingIcon;
    weight: IconProps["weight"];
}[] = [
    {
        key: "landing_solution_item_1",
        icon: ClipboardList,
        weight: "BoldDuotone",
    },
    {
        key: "landing_solution_item_2",
        icon: Database,
        weight: "BoldDuotone",
    },
    {
        key: "landing_solution_item_3",
        icon: PieChart,
        weight: "BoldDuotone",
    },
    {
        key: "landing_solution_item_4",
        icon: HandMoney,
        weight: "BoldDuotone",
    },
];

const howItWorksSteps: {
    number: string;
    icon: LandingIcon;
    weight: IconProps["weight"];
    titleKey: string;
    descKey: string;
}[] = [
    {
        number: "1",
        icon: RoundedMagnifier,
        weight: "BoldDuotone",
        titleKey: "landing_step1_title",
        descKey: "landing_step1_desc",
    },
    {
        number: "2",
        icon: GraphNewUp,
        weight: "BoldDuotone",
        titleKey: "landing_step2_title",
        descKey: "landing_step2_desc",
    },
    {
        number: "3",
        icon: DocumentText,
        weight: "BoldDuotone",
        titleKey: "landing_step3_title",
        descKey: "landing_step3_desc",
    },
];

const trustItems: {
    key: string;
    icon: LandingIcon;
    weight: IconProps["weight"];
}[] = [
    {
        key: "landing_trust_item_1",
        icon: Database,
        weight: "BoldDuotone",
    },
    {
        key: "landing_trust_item_2",
        icon: GraphNewUp,
        weight: "BoldDuotone",
    },
    {
        key: "landing_trust_item_3",
        icon: ShieldCheck,
        weight: "BoldDuotone",
    },
    {
        key: "landing_trust_item_4",
        icon: DocumentText,
        weight: "BoldDuotone",
    },
];

const warmGradientSectionClass =
    "border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)]";

const landingEase = [0.22, 1, 0.36, 1] as const;

const sectionVariants: Variants = {
    hidden: { opacity: 0, y: 18 },
    visible: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.42,
            ease: landingEase,
            when: "beforeChildren",
            staggerChildren: 0.05,
        },
    },
};

const containerVariants: Variants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.04,
        },
    },
};

const itemVariants: Variants = {
    hidden: { opacity: 0, y: 12, scale: 0.99 },
    visible: {
        opacity: 1,
        y: 0,
        scale: 1,
        transition: {
            duration: 0.3,
            ease: landingEase,
        },
    },
};

export default function Landing() {
    const { t, localePath } = useTranslation();
    const [url, setUrl] = useState("");
    const shouldReduceMotion = useReducedMotion();

    const heroMotionProps = shouldReduceMotion
        ? { initial: false }
        : { initial: "hidden" as const, animate: "visible" as const };

    const revealMotionProps = shouldReduceMotion
        ? { initial: false }
        : {
              initial: "hidden" as const,
              whileInView: "visible" as const,
              viewport: { once: true, amount: 0.08 },
          };

    const selectTypeFromPage = (type: ReportType) => {
        router.visit(localePath(`/get-report?type=${type}`));
    };

    const handleHeroRedirect = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        router.visit(localePath("/get-report"));
    };

    return (
        <PublicLayout>
            <Head title={t("landing_meta_title")} />

            <motion.section
                id="hero"
                className={warmGradientSectionClass}
                variants={sectionVariants}
                {...heroMotionProps}
            >
                <motion.div
                    className="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6 md:py-20 lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div
                        className="mx-auto max-w-3xl"
                        variants={itemVariants}
                    >
                        <p className="mb-5 text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_badge")}
                        </p>

                        <h1 className="text-[2.9rem] leading-[1.03] font-bold tracking-[-0.04em] text-brand-primary md:text-[4rem] md:leading-[1.02]">
                            {t("landing_hero_title_1")}
                        </h1>

                        <p className="mx-auto mt-5 max-w-2xl text-[1.08rem] leading-[1.72] text-brand-primary md:text-[1.15rem] md:leading-[1.7]">
                            {t("landing_hero_desc")}
                        </p>

                        <div className="mt-8 flex flex-col items-center gap-3">
                            {[
                                "landing_hero_benefit_1",
                                "landing_hero_benefit_2",
                                "landing_hero_benefit_3",
                            ].map((key) => (
                                <motion.div
                                    key={key}
                                    className="flex items-center gap-3 text-left text-[1.02rem] leading-[1.45] text-brand-primary md:text-[1.04rem]"
                                    variants={itemVariants}
                                >
                                    <CheckCircle
                                        size={18}
                                        weight="fill"
                                        className="shrink-0 text-brand-secondary"
                                    />
                                    <span>{t(key)}</span>
                                </motion.div>
                            ))}
                        </div>
                    </motion.div>

                    <motion.div
                        id="hero-form"
                        className="mx-auto mt-10 max-w-4xl"
                        variants={itemVariants}
                    >
                        <form
                            onSubmit={handleHeroRedirect}
                            className="mx-auto mt-6 max-w-3xl"
                        >
                            <div className="mx-auto flex max-w-3xl flex-col gap-3 sm:flex-row">
                                <Input
                                    id="landing-url"
                                    value={url}
                                    placeholder={t(
                                        "landing_hero_url_placeholder",
                                    )}
                                    onChange={(event) => {
                                        setUrl(event.target.value);
                                    }}
                                    className="h-12 solid-border border-brand-primary/30 bg-white text-[0.98rem] text-brand-primary placeholder:text-brand-primary/55"
                                />
                                <Button
                                    type="submit"
                                    className="h-12 min-w-full cursor-pointer bg-brand-secondary px-6 text-white hover:bg-brand-secondary/90 sm:min-w-48"
                                >
                                    {t("landing_generate_report")}
                                    <ArrowRight size={16} className="ml-2" />
                                </Button>
                            </div>
                        </form>
                    </motion.div>
                </motion.div>
            </motion.section>

            <motion.section
                id="report-example"
                className="border-b solid-divider bg-white py-14 md:py-18"
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(340px,1fr)_minmax(0,0.95fr)] lg:items-center lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div
                        className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)] p-4 transition-transform duration-300 hover:-translate-y-1 md:p-6"
                        variants={itemVariants}
                    >
                        <div className="border solid-border solid-border-warm-strong bg-white p-5">
                            <div className="flex items-center justify-between border-b solid-divider pb-3">
                                <div>
                                    <p className="text-xs uppercase tracking-[0.2em] text-brand-secondary">
                                        {t("landing_example_badge")}
                                    </p>
                                    <p className="mt-1 text-lg font-semibold text-brand-primary">
                                        {t("pdf_report_title")}
                                    </p>
                                </div>
                                <DocumentText
                                    size={28}
                                    weight="BoldDuotone"
                                    className="text-brand-secondary"
                                />
                            </div>

                            <div className="mt-5 grid gap-3 sm:grid-cols-2">
                                {[
                                    {
                                        label: t("pdf_overall_score"),
                                        value: "84/100",
                                    },
                                    {
                                        label: t("pdf_estimated_market_value"),
                                        value: "€168.000",
                                    },
                                    {
                                        label: t("pdf_area_analysis"),
                                        value: "8.6/10",
                                    },
                                    {
                                        label: t("pdf_gross_yield"),
                                        value: "6.2%",
                                    },
                                ].map((item) => (
                                    <div
                                        key={item.label}
                                        className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#fff9f4_100%)] p-3"
                                    >
                                        <p className="text-[11px] uppercase tracking-[0.18em] text-brand-primary/70">
                                            {item.label}
                                        </p>
                                        <p className="mt-2 text-xl font-semibold text-brand-primary">
                                            {item.value}
                                        </p>
                                    </div>
                                ))}
                            </div>

                            <div className="mt-4 h-44 border border-dashed solid-border solid-border-warm-strong bg-[repeating-linear-gradient(135deg,#fff_0,#fff_16px,#f7f4ef_16px,#f7f4ef_32px)]" />
                            <p className="mt-3 text-sm text-brand-primary">
                                {t("landing_example_placeholder")}
                            </p>
                        </div>
                    </motion.div>

                    <motion.div
                        className="mx-auto max-w-2xl text-center lg:text-left"
                        variants={itemVariants}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_example_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] leading-[1.06] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem] md:leading-[1.08]">
                            {t("landing_example_title")}
                        </h2>
                        <p className="mt-4 text-[1.04rem] leading-[1.72] text-brand-primary md:text-lg md:leading-[1.7]">
                            {t("landing_example_desc")}
                        </p>

                        <div className="mt-8 grid gap-3 text-left sm:grid-cols-2">
                            {exampleFeatures.map(
                                ({ icon: Icon, key, weight }) => (
                                    <motion.div
                                        key={key}
                                        className="flex items-start gap-3 border solid-border solid-border-warm bg-white p-4 transition-transform duration-300 hover:-translate-y-1"
                                        variants={itemVariants}
                                    >
                                        <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-secondary/12 text-brand-secondary">
                                            <Icon size={20} weight={weight} />
                                        </span>
                                        <span className="flex-1 text-[1rem] leading-[1.55] text-brand-primary md:text-[0.98rem]">
                                            {t(key)}
                                        </span>
                                    </motion.div>
                                ),
                            )}
                        </div>
                    </motion.div>
                </motion.div>
            </motion.section>

            <motion.section
                id="problem"
                className={`${warmGradientSectionClass} py-14 md:py-18`}
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div
                        className="mx-auto max-w-3xl text-center"
                        variants={itemVariants}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_problem_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] leading-[1.06] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem] md:leading-[1.08]">
                            {t("landing_problem_title")}
                        </h2>
                    </motion.div>

                    <div className="mt-10 grid gap-5 lg:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)]">
                        <motion.div
                            className="flex h-full flex-col justify-between border border-brand-primary bg-brand-primary p-7 text-white"
                            variants={itemVariants}
                        >
                            <div>
                                <p className="mt-2 text-[1.7rem] font-bold leading-[1.08] tracking-[-0.03em] md:mt-8 md:text-3xl md:leading-[1.1]">
                                    {t("landing_problem_quote_1")}
                                </p>
                                <p className="mt-3 text-[1.28rem] leading-[1.2] text-white/90 md:text-2xl md:leading-tight">
                                    {t("landing_problem_quote_2")}
                                </p>
                            </div>
                        </motion.div>

                        <div className="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                            {problemItems.map(
                                ({ key, icon: Icon, weight }, index) => (
                                    <motion.div
                                        key={key}
                                        className="border solid-border solid-border-warm bg-white p-5 transition-transform duration-300 hover:-translate-y-1"
                                        variants={itemVariants}
                                    >
                                        <div className="mb-4 flex items-center justify-between border-b solid-divider pb-3">
                                            <span className="text-sm font-semibold tracking-[0.16em] text-brand-secondary">
                                                0{index + 1}
                                            </span>
                                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-[#fff2ee] text-[#d14d3f]">
                                                <Icon
                                                    size={19}
                                                    weight={weight}
                                                />
                                            </span>
                                        </div>
                                        <p className="text-[1.04rem] leading-[1.68] text-brand-primary md:text-base md:leading-[1.7]">
                                            {t(key)}
                                        </p>
                                    </motion.div>
                                ),
                            )}
                        </div>
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="solution"
                className="border-b solid-divider bg-white py-14 md:py-18"
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div
                        className="mx-auto max-w-3xl text-center"
                        variants={itemVariants}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_solution_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] leading-[1.06] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem] md:leading-[1.08]">
                            {t("landing_solution_title")}
                        </h2>
                    </motion.div>

                    <div className="mt-8 grid gap-4 md:grid-cols-2">
                        {solutionItems.map(({ key, icon: Icon, weight }) => (
                            <motion.div
                                key={key}
                                className="flex items-center gap-3 border solid-border solid-border-warm bg-[#fcfbf8] p-5 transition-transform duration-300 hover:-translate-y-1"
                                variants={itemVariants}
                            >
                                <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-secondary/12 text-brand-secondary">
                                    <Icon size={20} weight={weight} />
                                </span>
                                <span className="flex-1 text-[1.04rem] leading-[1.6] text-brand-primary md:text-base md:leading-relaxed">
                                    {t(key)}
                                </span>
                            </motion.div>
                        ))}
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="how-it-works"
                className="border-b solid-divider bg-white py-14 md:py-18"
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div
                        className="mx-auto max-w-3xl text-center"
                        variants={itemVariants}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("how_it_works")}
                        </p>
                        <h2 className="mt-3 text-[2rem] leading-[1.06] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem] md:leading-[1.08]">
                            {t("landing_how_title")}
                        </h2>
                    </motion.div>

                    <div className="mt-8 grid gap-4 md:grid-cols-3">
                        {howItWorksSteps.map(
                            ({
                                number,
                                icon: Icon,
                                weight,
                                titleKey,
                                descKey,
                            }) => (
                                <motion.div
                                    key={number}
                                    className="border solid-border solid-border-warm bg-[#fbfaf8] p-5 transition-transform duration-300 hover:-translate-y-1"
                                    variants={itemVariants}
                                >
                                    <div className="flex items-center justify-between border-b solid-divider pb-4">
                                        <span className="text-3xl font-bold tracking-[-0.05em] text-brand-primary">
                                            {number}
                                        </span>
                                        <span className="flex h-12 w-12 items-center justify-center rounded-full bg-brand-secondary/12 text-brand-secondary">
                                            <Icon size={22} weight={weight} />
                                        </span>
                                    </div>
                                    <h3 className="mt-4 text-lg font-semibold text-brand-primary">
                                        {t(titleKey)}
                                    </h3>
                                    <p className="mt-2 text-[1rem] leading-[1.62] text-brand-primary md:text-sm md:leading-relaxed">
                                        {t(descKey)}
                                    </p>
                                </motion.div>
                            ),
                        )}
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="pricing"
                className={`${warmGradientSectionClass} py-14 md:py-18`}
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div
                        className="mx-auto max-w-3xl text-center"
                        variants={itemVariants}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_pricing_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] leading-[1.06] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem] md:leading-[1.08]">
                            {t("landing_pricing_title")}
                        </h2>
                        <p className="mt-4 text-[1.04rem] leading-[1.72] text-brand-primary md:text-lg md:leading-[1.7]">
                            {t("landing_pricing_desc")}
                        </p>
                    </motion.div>

                    <div className="mt-8 grid gap-4 md:grid-cols-2">
                        {typeOptions.map(
                            ({
                                type,
                                icon: Icon,
                                iconWeight,
                                labelKey,
                                priceKey,
                            }) => (
                                <motion.div
                                    key={type}
                                    className={`border solid-border p-6 ${
                                        type === "buying_living"
                                            ? "border-brand-primary bg-brand-primary text-white"
                                            : "solid-border-warm bg-white"
                                    } transition-transform duration-300 hover:-translate-y-1`}
                                    variants={itemVariants}
                                >
                                    <div className="flex items-center justify-between gap-4">
                                        <div className="flex items-center gap-3">
                                            <div
                                                className={`flex h-11 w-11 items-center justify-center ${
                                                    type === "buying_living"
                                                        ? "bg-white/10"
                                                        : "bg-brand-primary/5"
                                                }`}
                                            >
                                                <Icon
                                                    size={22}
                                                    weight={iconWeight}
                                                    className={
                                                        type === "buying_living"
                                                            ? "text-white"
                                                            : "text-brand-secondary"
                                                    }
                                                />
                                            </div>
                                            <h3 className="text-xl font-semibold">
                                                {t(labelKey)}
                                            </h3>
                                        </div>
                                        <span
                                            className={`text-sm font-semibold ${
                                                type === "buying_living"
                                                    ? "text-white/70"
                                                    : "text-brand-primary"
                                            }`}
                                        >
                                            {t("landing_price_vat_included")}
                                        </span>
                                    </div>

                                    <p className="mt-8 text-4xl font-bold tracking-[-0.04em]">
                                        {t(priceKey)}
                                    </p>

                                    <button
                                        type="button"
                                        onClick={() => selectTypeFromPage(type)}
                                        className={`mt-8 inline-flex items-center gap-2 border solid-border px-4 py-3 text-sm font-semibold transition-colors ${
                                            type === "buying_living"
                                                ? "border-white/35 bg-white text-brand-primary hover:bg-white/90"
                                                : "border-brand-primary bg-brand-primary text-white hover:bg-brand-primary/92"
                                        }`}
                                    >
                                        {t("landing_pricing_cta")}
                                        <ArrowRight size={15} />
                                    </button>
                                </motion.div>
                            ),
                        )}
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="trust"
                className="border-b solid-divider bg-white py-14 md:py-18"
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_minmax(300px,0.7fr)] lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div variants={itemVariants}>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_trust_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] leading-[1.06] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem] md:leading-[1.08]">
                            {t("landing_trust_title")}
                        </h2>

                        <div className="mt-8 space-y-3">
                            {trustItems.map(({ key, icon: Icon, weight }) => (
                                <motion.div
                                    key={key}
                                    className="flex items-start gap-3 border solid-border solid-border-warm bg-[#fcfbf8] p-4"
                                    variants={itemVariants}
                                >
                                    <span className="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-secondary/12 text-brand-secondary">
                                        <Icon size={19} weight={weight} />
                                    </span>
                                    <span className="text-[1.04rem] leading-[1.62] text-brand-primary md:text-base">
                                        {t(key)}
                                    </span>
                                </motion.div>
                            ))}
                        </div>
                    </motion.div>

                    <motion.aside
                        className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)] p-6 lg:self-start"
                        variants={itemVariants}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.2em] text-brand-secondary">
                            {t("landing_disclaimer_title")}
                        </p>
                        <p className="mt-3 text-[1rem] leading-[1.62] text-brand-primary md:text-sm md:leading-relaxed">
                            {t("landing_disclaimer_desc")}
                        </p>
                    </motion.aside>
                </motion.div>
            </motion.section>

            <motion.section
                id="final-cta"
                className={`${warmGradientSectionClass} py-14 md:py-16`}
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8"
                    variants={containerVariants}
                >
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary/90">
                        {t("landing_cta_badge")}
                    </p>
                    <h2 className="mt-4 text-[2rem] leading-[1.06] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem] md:leading-[1.08]">
                        {t("landing_cta_title")}
                    </h2>
                    <p className="mt-4 text-[1.04rem] leading-[1.68] text-brand-primary md:text-lg md:leading-relaxed">
                        {t("landing_cta_desc")}
                    </p>
                    <a
                        href="#hero-form"
                        className="mt-8 inline-flex items-center gap-2 border solid-border solid-border-warm-strong bg-white px-6 py-3 text-sm font-semibold text-brand-primary transition-colors hover:bg-white/90"
                    >
                        {t("landing_cta_primary")}
                        <ArrowRight size={15} />
                    </a>
                </motion.div>
            </motion.section>
        </PublicLayout>
    );
}
