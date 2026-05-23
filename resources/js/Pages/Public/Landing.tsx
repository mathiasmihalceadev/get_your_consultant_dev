import { type ComponentType, type FormEvent, useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import {
    ArrowRight,
    ArrowsLeftRight,
    ChartLineUp,
    CheckCircle,
    CurrencyEur,
    FilePdf,
    LinkSimple,
    MagnifyingGlass,
    Scales,
    Star,
    TrendUp,
    WarningCircle,
} from "@phosphor-icons/react";
import { motion, useReducedMotion, type Variants } from "framer-motion";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { landingAssets } from "@/lib/landingAssets";
import { ReportType } from "@/types";

type LandingIcon = ComponentType<any>;

type PricingOption = {
    type: ReportType;
    labelKey: string;
    priceKey: string;
    imageSrc: string;
    imageAltKey: string;
    featureKeys: string[];
};

type PricingCatalogEntry = {
    checkout_currency: string;
    checkout_amount_minor: number;
};

interface LandingProps {
    pricingCatalog: Partial<Record<ReportType, PricingCatalogEntry>>;
}

const heroBenefitKeys = [
    "landing_hero_benefit_1",
    "landing_hero_benefit_2",
    "landing_hero_benefit_3",
] as const;

const exampleFeatureItems: {
    icon: LandingIcon;
    labelKey: string;
}[] = [
    { icon: CurrencyEur, labelKey: "landing_example_feature_1" },
    { icon: ArrowsLeftRight, labelKey: "landing_example_feature_2" },
    { icon: WarningCircle, labelKey: "landing_example_feature_3" },
    { icon: Scales, labelKey: "landing_example_feature_4" },
    { icon: ChartLineUp, labelKey: "landing_example_feature_5" },
];

const whyCards: {
    icon: LandingIcon;
    titleKey: string;
    bodyKey: string;
}[] = [
    {
        icon: CurrencyEur,
        titleKey: "landing_why_item_1_title",
        bodyKey: "landing_why_item_1_desc",
    },
    {
        icon: ArrowsLeftRight,
        titleKey: "landing_why_item_2_title",
        bodyKey: "landing_why_item_2_desc",
    },
    {
        icon: WarningCircle,
        titleKey: "landing_why_item_3_title",
        bodyKey: "landing_why_item_3_desc",
    },
    {
        icon: TrendUp,
        titleKey: "landing_why_item_4_title",
        bodyKey: "landing_why_item_4_desc",
    },
];

const howCards: {
    number: string;
    icon: LandingIcon;
    titleKey: string;
    bodyKey: string;
}[] = [
    {
        number: "1",
        icon: LinkSimple,
        titleKey: "landing_process_item_1_title",
        bodyKey: "landing_process_item_1_desc",
    },
    {
        number: "2",
        icon: MagnifyingGlass,
        titleKey: "landing_process_item_2_title",
        bodyKey: "landing_process_item_2_desc",
    },
    {
        number: "3",
        icon: FilePdf,
        titleKey: "landing_process_item_3_title",
        bodyKey: "landing_process_item_3_desc",
    },
];

const pricingOptions: PricingOption[] = [
    {
        type: "buying_living",
        labelKey: "buying",
        priceKey: "landing_pricing_buying_price",
        imageSrc: landingAssets.pricingBuyingImageSrc,
        imageAltKey: "landing_pricing_buying_visual_label",
        featureKeys: [
            "landing_pricing_buying_feature_1",
            "landing_pricing_buying_feature_2",
            "landing_pricing_buying_feature_3",
            "landing_pricing_buying_feature_4",
        ],
    },
    {
        type: "rental_living",
        labelKey: "rental",
        priceKey: "landing_pricing_rental_price",
        imageSrc: landingAssets.pricingRentalImageSrc,
        imageAltKey: "landing_pricing_rental_visual_label",
        featureKeys: [
            "landing_pricing_rental_feature_1",
            "landing_pricing_rental_feature_2",
            "landing_pricing_rental_feature_3",
            "landing_pricing_rental_feature_4",
        ],
    },
];

const warmGradientSectionClass =
    "border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]";

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

export default function Landing({ pricingCatalog }: LandingProps) {
    const { t, locale, localePath } = useTranslation();
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

    const sampleReportHref =
        locale === "ro"
            ? "/images/report-example-ro.pdf"
            : "/images/report-example-en.pdf";
    const heroImageSrc = landingAssets.heroImageSrc;
    const reportImageSrc = landingAssets.reportImageSrc;
    const ctaImageSrc = landingAssets.ctaImageSrc;
    const textureImageSrc = landingAssets.textureImageSrc;

    const selectTypeFromPage = (type: ReportType) => {
        router.visit(localePath(`/get-report?type=${type}`));
    };

    const formatCatalogPrice = (type: ReportType, fallbackKey: string) => {
        const pricing = pricingCatalog?.[type];

        if (!pricing) {
            return t(fallbackKey);
        }

        return new Intl.NumberFormat(locale === "ro" ? "ro-RO" : "en-IE", {
            style: "currency",
            currency: pricing.checkout_currency.toUpperCase(),
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(pricing.checkout_amount_minor / 100);
    };

    const handleHeroRedirect = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        router.visit(localePath("/get-report"));
    };

    const renderLeadForm = (sectionId: string) => (
        <form id={sectionId} onSubmit={handleHeroRedirect} className="w-full">
            <div className="flex flex-col gap-0 sm:flex-row sm:items-stretch">
                <Input
                    id={`${sectionId}-url`}
                    value={url}
                    placeholder={t("landing_hero_url_placeholder")}
                    onChange={(event) => {
                        setUrl(event.target.value);
                    }}
                    className="h-14 border border-brand-primary/12 bg-[#fff] px-5 text-base text-brand-primary shadow-[0_16px_40px_rgba(52,48,106,0.08)] placeholder:text-brand-primary/45"
                />
                <Button
                    type="submit"
                    className="h-14 bg-brand-primary px-7 text-[0.98rem] font-semibold text-white shadow-[0_18px_36px_rgba(52,48,106,0.22)] hover:bg-brand-primary/92 sm:-ml-px"
                >
                    {t("landing_generate_report")}
                    <ArrowRight size={16} className="ml-2" />
                </Button>
            </div>
        </form>
    );

    return (
        <PublicLayout>
            <Head title={t("landing_meta_title")} />

            <motion.section
                id="hero"
                className={`relative overflow-hidden ${warmGradientSectionClass}`}
                variants={sectionVariants}
                {...heroMotionProps}
            >
                <img
                    src={textureImageSrc}
                    alt=""
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.07] mix-blend-multiply"
                />
                <motion.div
                    className="relative mx-auto grid max-w-6xl gap-12 px-4 py-14 sm:px-6 md:py-18 lg:grid-cols-[minmax(0,0.98fr)_minmax(360px,0.92fr)] lg:items-center lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div className="max-w-2xl" variants={itemVariants}>
                        <h1 className="text-[2.45rem] leading-[0.98] font-extrabold tracking-[-0.05em] text-brand-primary md:text-[3.7rem] md:leading-[0.95]">
                            {t("landing_hero_title_1")}
                        </h1>
                        <p className="mt-4 max-w-xl text-[14px] leading-[1.6] text-brand-primary/82 md:mt-5 md:text-[1.05rem] md:leading-[1.72]">
                            {t("landing_hero_desc")}
                        </p>

                        <div className="mt-7 max-w-xl md:mt-8">
                            {renderLeadForm("hero-form")}
                        </div>

                        <div className="mt-5 flex flex-wrap gap-x-5 gap-y-2 md:mt-6 md:gap-x-6">
                            {heroBenefitKeys.map((key) => (
                                <motion.div
                                    key={key}
                                    className="flex items-center gap-2 text-[14px] font-semibold text-brand-primary md:text-[1.05rem]"
                                    variants={itemVariants}
                                >
                                    <CheckCircle
                                        size={18}
                                        weight="fill"
                                        className="shrink-0 text-brand-secondary"
                                    />
                                    <span className="leading-[1.35]">
                                        {t(key)}
                                    </span>
                                </motion.div>
                            ))}
                        </div>

                        <motion.div
                            className="mt-7 max-w-xl"
                            variants={itemVariants}
                        >
                            <div className="flex flex-wrap items-center gap-x-4 gap-y-2">
                                <p className="text-[14px] font-bold text-brand-primary">
                                    {t("landing_social_proof_summary")}
                                </p>
                                <div className="flex items-center gap-1 text-[#f3b44f]">
                                    {Array.from({ length: 5 }).map(
                                        (_, index) => (
                                            <Star
                                                key={index}
                                                size={16}
                                                weight="fill"
                                            />
                                        ),
                                    )}
                                </div>
                            </div>
                            <p className="mt-2 max-w-[32rem] text-[14px] leading-[1.6] text-brand-primary/76">
                                {t("landing_social_proof_note")}
                            </p>
                        </motion.div>
                    </motion.div>

                    <motion.div variants={itemVariants}>
                        <div className="relative flex min-h-[332px] items-center justify-center py-1 md:min-h-[620px] md:p-2">
                            <div className="absolute inset-x-[10%] bottom-10 h-20 rounded-full bg-brand-primary/14 blur-3xl" />
                            <div className="relative flex min-h-[300px] w-full items-center justify-center md:min-h-[580px]">
                                <img
                                    src={heroImageSrc}
                                    alt={t("landing_hero_visual_title")}
                                    className="relative z-10 max-h-[332px] w-full scale-100 object-contain drop-shadow-[0_24px_40px_rgba(52,48,106,0.16)] md:max-h-[620px] scale-[1.2] md:scale-[1.3] md:drop-shadow-[0_34px_56px_rgba(52,48,106,0.22)]"
                                />
                            </div>
                        </div>
                    </motion.div>
                </motion.div>
            </motion.section>

            <motion.section
                id="why-check"
                className="border-b border-brand-primary/85 bg-brand-primary py-16 md:py-18"
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
                        <h2 className="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-white md:text-[2.8rem]">
                            {t("landing_why_title")}
                        </h2>
                        <p className="mt-3 text-[14px] leading-[1.6] text-white/78 md:mt-4 md:text-lg md:leading-[1.72]">
                            {t("landing_why_desc")}
                        </p>
                    </motion.div>

                    <div className="mt-8 grid grid-cols-2 gap-3 md:mt-10 md:gap-4 xl:grid-cols-4">
                        {whyCards.map(({ icon: Icon, titleKey, bodyKey }) => (
                            <motion.div
                                key={titleKey}
                                className="bg-transparent px-3 py-4 text-center md:px-6 md:py-8"
                                variants={itemVariants}
                            >
                                <div className="flex items-center justify-center text-white">
                                    <Icon
                                        weight="bold"
                                        className="h-9 w-9 md:h-14 md:w-14"
                                    />
                                </div>
                                <h3 className="mt-3 text-[0.95rem] font-semibold leading-[1.18] text-white md:mt-6 md:text-xl">
                                    {t(titleKey)}
                                </h3>
                                <p className="mt-1.5 text-[12px] leading-[1.45] text-white/78 md:mt-3 md:text-[1rem] md:leading-[1.68]">
                                    {t(bodyKey)}
                                </p>
                            </motion.div>
                        ))}
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="report-example"
                className="relative overflow-hidden solid-divider bg-white pb-16 pt-12 md:py-18"
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <img
                    src={textureImageSrc}
                    alt=""
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.045] mix-blend-multiply"
                />
                <motion.div
                    className="relative mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-[minmax(340px,0.94fr)_minmax(0,1.06fr)] lg:items-center lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div variants={itemVariants}>
                        <div className="relative flex min-h-[332px] items-center justify-center py-1 md:min-h-[620px] md:p-2">
                            <div className="absolute inset-x-[16%] bottom-12 h-18 rounded-full bg-brand-primary/12 blur-3xl" />
                            <img
                                src={reportImageSrc}
                                alt={t("landing_example_visual_title")}
                                className="relative z-10 max-h-[332px] w-full scale-100 object-contain drop-shadow-[0_22px_34px_rgba(52,48,106,0.14)] md:max-h-[620px] md:drop-shadow-[0_32px_48px_rgba(52,48,106,0.2)]"
                            />
                        </div>
                    </motion.div>

                    <motion.div className="max-w-2xl" variants={itemVariants}>
                        <h2 className="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">
                            {t("landing_example_title")}
                        </h2>
                        <p className="mt-3 text-[14px] leading-[1.6] text-brand-primary/78 md:mt-4 md:text-lg md:leading-[1.76]">
                            {t("landing_example_desc")}
                        </p>
                        <div className="mt-5 grid gap-3 sm:grid-cols-2 md:mt-6">
                            {exampleFeatureItems.map(
                                ({ icon: Icon, labelKey }) => (
                                    <div
                                        key={labelKey}
                                        className="flex items-center gap-3 border border-brand-primary/10 bg-white px-4 py-4 shadow-[0_10px_24px_rgba(52,48,106,0.06)]"
                                    >
                                        <Icon
                                            size={20}
                                            weight="bold"
                                            className="text-brand-secondary"
                                        />
                                        <span className="text-[14px] leading-[1.45] text-brand-primary md:text-[1rem]">
                                            {t(labelKey)}
                                        </span>
                                    </div>
                                ),
                            )}
                        </div>
                        <a
                            href={sampleReportHref}
                            target="_blank"
                            rel="noreferrer"
                            className="mt-8 inline-flex items-center gap-2 bg-brand-primary px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_34px_rgba(52,48,106,0.18)] transition-colors hover:bg-brand-primary/92"
                        >
                            {t("landing_example_cta")}
                            <ArrowRight size={16} />
                        </a>
                    </motion.div>
                </motion.div>
            </motion.section>

            <motion.section
                id="how-it-works"
                className="solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f8faff_100%)] py-16 md:pt-0 md:pb-16"
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
                        <h2 className="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">
                            {t("landing_how_title")}
                        </h2>
                        <p className="mt-3 text-[14px] leading-[1.6] text-brand-primary/74 md:mt-4 md:text-lg md:leading-[1.72]">
                            {t("landing_how_desc")}
                        </p>
                    </motion.div>

                    <div className="mt-8 grid gap-5 lg:mt-10 lg:grid-cols-3 lg:gap-8">
                        {howCards.map(
                            (
                                { number, icon: Icon, titleKey, bodyKey },
                                index,
                            ) => (
                                <motion.div
                                    key={number}
                                    className="relative"
                                    variants={itemVariants}
                                >
                                    {index < howCards.length - 1 ? (
                                        <div className="pointer-events-none absolute right-[-26px] top-[82px] hidden h-12 w-12 items-center justify-center rounded-full border border-brand-primary/10 bg-white text-brand-primary shadow-[0_12px_28px_rgba(52,48,106,0.08)] lg:flex">
                                            <ArrowRight
                                                size={20}
                                                weight="bold"
                                            />
                                        </div>
                                    ) : null}

                                    <div className="h-full border border-brand-primary/10 bg-white px-6 py-7 shadow-[0_18px_42px_rgba(52,48,106,0.08)]">
                                        <div className="relative flex h-22 w-22 items-center justify-center rounded-full bg-[#f3f6ff] text-brand-primary">
                                            <span className="absolute left-0 top-0 flex h-8 w-8 -translate-x-1/4 -translate-y-1/4 items-center justify-center rounded-full bg-brand-primary text-sm font-semibold text-white shadow-[0_10px_22px_rgba(52,48,106,0.24)]">
                                                {number}
                                            </span>
                                            <Icon size={42} weight="bold" />
                                        </div>
                                        <h3 className="mt-5 text-lg font-semibold text-brand-primary md:mt-6 md:text-xl">
                                            {t(titleKey)}
                                        </h3>
                                        <p className="mt-2 text-[14px] leading-[1.6] text-brand-primary/76 md:mt-3 md:text-[1rem] md:leading-[1.7]">
                                            {t(bodyKey)}
                                        </p>
                                    </div>
                                </motion.div>
                            ),
                        )}
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="reviews"
                className="border-b solid-divider bg-white py-10 md:py-12"
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <motion.div
                    className="mx-auto grid max-w-6xl gap-6 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div className="max-w-3xl" variants={itemVariants}>
                        <h2 className="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.4rem]">
                            {t("landing_reviews_title")}
                        </h2>
                        <p className="mt-3 max-w-2xl text-[14px] leading-[1.6] text-brand-primary/76 md:text-base md:leading-[1.7]">
                            {t("landing_reviews_desc")}
                        </p>
                    </motion.div>

                    <div
                        className="flex flex-col gap-4 sm:flex-row lg:justify-end"
                        aria-label="review-stats"
                    >
                        <div className="border border-brand-primary/10 bg-[linear-gradient(180deg,#ffffff_0%,#eef2ff_100%)] px-5 py-5 shadow-[0_14px_34px_rgba(52,48,106,0.08)] sm:w-[320px]">
                            <div className="flex flex-col gap-3">
                                <p className="text-[1.7rem] font-bold leading-[0.95] tracking-[-0.05em] text-brand-primary md:text-[2.2rem] md:tracking-[-0.06em]">
                                    {t("landing_social_proof_summary")}
                                </p>
                                <div className="flex items-center gap-1 text-[#f3b44f]">
                                    {Array.from({ length: 5 }).map(
                                        (_, index) => (
                                            <Star
                                                key={index}
                                                size={16}
                                                weight="fill"
                                            />
                                        ),
                                    )}
                                </div>
                            </div>
                            <p className="mt-3 max-w-[16rem] text-[14px] leading-[1.6] text-brand-primary/74 md:text-base md:leading-[1.7]">
                                {t("landing_reviews_stat_reports_body")}
                            </p>
                        </div>
                        <div className="border border-brand-primary/10 bg-brand-primary px-5 py-5 text-white shadow-[0_18px_44px_rgba(52,48,106,0.16)] sm:w-[320px]">
                            <div className="flex items-end gap-3">
                                <p className="text-[1.7rem] font-bold leading-none tracking-[-0.05em] md:text-[2.2rem] md:tracking-[-0.06em]">
                                    4.9/5
                                </p>
                                <div className="mb-1 flex items-center gap-1 text-[#f3b44f]">
                                    {Array.from({ length: 5 }).map(
                                        (_, index) => (
                                            <Star
                                                key={index}
                                                size={16}
                                                weight="fill"
                                            />
                                        ),
                                    )}
                                </div>
                            </div>
                            <p className="mt-3 text-[14px] leading-[1.6] text-white/76 md:text-base md:leading-[1.7]">
                                {t("landing_reviews_stat_rating_body")}
                            </p>
                        </div>
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="pricing"
                className={`relative overflow-hidden ${warmGradientSectionClass} py-16 md:py-18`}
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <img
                    src={textureImageSrc}
                    alt=""
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.06] mix-blend-multiply"
                />
                <motion.div
                    className="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div
                        className="mx-auto max-w-3xl text-center"
                        variants={itemVariants}
                    >
                        <h2 className="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">
                            {t("landing_pricing_title")}
                        </h2>
                        <p className="mt-3 text-[14px] leading-[1.6] text-brand-primary/76 md:mt-4 md:text-lg md:leading-[1.74]">
                            {t("landing_pricing_desc")}
                        </p>
                    </motion.div>

                    <div className="mt-10 flex flex-col items-center gap-5 lg:flex-row lg:justify-center">
                        {pricingOptions.map(
                            ({
                                type,
                                labelKey,
                                priceKey,
                                imageSrc,
                                imageAltKey,
                                featureKeys,
                            }) => (
                                <motion.div
                                    key={type}
                                    className="w-full max-w-[420px] overflow-hidden border border-brand-primary/10 bg-white text-brand-primary shadow-[0_22px_54px_rgba(52,48,106,0.08)]"
                                    variants={itemVariants}
                                >
                                    <div className="relative flex h-72 items-center justify-center overflow-hidden border-b border-brand-primary/10 bg-white p-6 md:h-84 md:p-8">
                                        <div className="absolute inset-x-10 bottom-5 h-8 rounded-full bg-brand-primary/6 blur-xl" />
                                        <img
                                            src={imageSrc}
                                            alt={t(imageAltKey)}
                                            className="relative z-10 h-full w-full scale-[1.16] object-contain drop-shadow-[0_10px_18px_rgba(52,48,106,0.08)]"
                                        />
                                    </div>

                                    <div className="px-6 py-6">
                                        <div className="flex items-start justify-between gap-4">
                                            <h3 className="text-xl font-semibold text-brand-primary">
                                                {t(labelKey)}
                                            </h3>
                                            <div className="text-right">
                                                <p className="text-3xl font-bold tracking-[-0.05em] text-brand-primary">
                                                    {formatCatalogPrice(
                                                        type,
                                                        priceKey,
                                                    )}
                                                </p>
                                                <p className="mt-1 text-xs font-semibold text-brand-primary/54">
                                                    {t(
                                                        "landing_price_vat_included",
                                                    )}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="mt-6 space-y-2">
                                            {featureKeys.map((featureKey) => (
                                                <div
                                                    key={featureKey}
                                                    className="flex items-start gap-3"
                                                >
                                                    <CheckCircle
                                                        size={18}
                                                        weight="fill"
                                                        className="mt-0.5 text-brand-secondary"
                                                    />
                                                    <span className="text-[14px] leading-[1.6] text-brand-primary/76 md:text-sm">
                                                        {t(featureKey)}
                                                    </span>
                                                </div>
                                            ))}
                                        </div>

                                        <button
                                            type="button"
                                            onClick={() =>
                                                selectTypeFromPage(type)
                                            }
                                            className="mt-7 inline-flex cursor-pointer items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92"
                                        >
                                            {t("landing_pricing_cta")}
                                            <ArrowRight size={16} />
                                        </button>
                                    </div>
                                </motion.div>
                            ),
                        )}
                    </div>
                </motion.div>
            </motion.section>

            <motion.section
                id="final-cta"
                className="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#eaf1ff_0%,#f5f8ff_100%)] py-16 md:py-8"
                variants={sectionVariants}
                {...revealMotionProps}
            >
                <img
                    src={textureImageSrc}
                    alt=""
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.07] mix-blend-multiply"
                />
                <motion.div
                    className="relative mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-[minmax(0,0.98fr)_minmax(320px,0.9fr)] lg:items-center lg:px-8"
                    variants={containerVariants}
                >
                    <motion.div className="max-w-2xl" variants={itemVariants}>
                        <h2 className="text-[2rem] leading-[1.04] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.8rem]">
                            {t("landing_cta_title")}
                        </h2>
                        <p className="mt-3 text-[14px] leading-[1.6] text-brand-primary/78 md:mt-4 md:text-lg md:leading-[1.74]">
                            {t("landing_cta_desc")}
                        </p>

                        <div className="mt-7 max-w-xl md:mt-8">
                            {renderLeadForm("final-cta-form")}
                        </div>

                        <div className="mt-5 flex items-start gap-3 text-[14px] leading-[1.6] font-medium text-brand-primary/74 md:text-sm">
                            <CheckCircle
                                size={24}
                                weight="fill"
                                className="mt-0.5 shrink-0 text-brand-secondary"
                            />
                            {t("landing_cta_support_note")}
                        </div>
                    </motion.div>

                    <motion.div variants={itemVariants}>
                        <div className="relative flex min-h-[332px] items-end justify-center py-1 md:min-h-[460px] md:p-4">
                            <div className="absolute inset-x-[14%] bottom-10 h-18 rounded-full bg-brand-primary/12 blur-3xl" />
                            <div className="relative flex min-h-[300px] w-full items-end justify-center md:min-h-[420px]">
                                <img
                                    src={ctaImageSrc}
                                    alt={t("landing_cta_visual_title")}
                                    className="relative z-10 max-h-[332px] w-full scale-100 object-contain drop-shadow-[0_20px_28px_rgba(52,48,106,0.12)] md:max-h-[450px] md:scale-[1.04] md:drop-shadow-[0_30px_44px_rgba(52,48,106,0.18)]"
                                />
                            </div>
                        </div>
                    </motion.div>
                </motion.div>
            </motion.section>
        </PublicLayout>
    );
}
