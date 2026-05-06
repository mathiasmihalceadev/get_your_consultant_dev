import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import {
    ArrowRight,
    ChartLineUp,
    CheckCircle,
    FilePdf,
    House,
    Key,
    MagnifyingGlass,
    MapPin,
    Scales,
    ShieldCheck,
} from "@phosphor-icons/react";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { ReportType } from "@/types";

const typeOptions: {
    type: ReportType;
    icon: typeof House;
    labelKey: string;
    priceKey: string;
}[] = [
    {
        type: "buying_living",
        icon: House,
        labelKey: "buying",
        priceKey: "landing_pricing_buying_price",
    },
    {
        type: "rental_living",
        icon: Key,
        labelKey: "rental",
        priceKey: "landing_pricing_rental_price",
    },
];

const warmGradientSectionClass =
    "border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)]";

export default function Landing() {
    const { t, localePath } = useTranslation();
    const [url, setUrl] = useState("");

    const selectTypeFromPage = (type: ReportType) => {
        router.visit(localePath(`/get-report?type=${type}`));
    };

    const handleHeroRedirect = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        router.visit(localePath("/get-report"));
    };

    return (
        <PublicLayout>
            <Head title={t("landing_meta_title")} />

            <section id="hero" className={warmGradientSectionClass}>
                <div className="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6 md:py-20 lg:px-8">
                    <div className="mx-auto max-w-3xl">
                        <p className="mb-5 text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_badge")}
                        </p>

                        <h1 className="text-[2.9rem] font-bold tracking-[-0.04em] text-brand-primary md:text-[4rem] md:leading-[1.02]">
                            {t("landing_hero_title_1")}
                        </h1>

                        <p className="mx-auto mt-5 max-w-2xl text-lg leading-[1.7] text-brand-primary md:text-[1.15rem]">
                            {t("landing_hero_desc")}
                        </p>

                        <div className="mt-8 flex flex-col items-center gap-3">
                            {[
                                "landing_hero_benefit_1",
                                "landing_hero_benefit_2",
                                "landing_hero_benefit_3",
                            ].map((key) => (
                                <div
                                    key={key}
                                    className="flex items-center gap-3 text-[1.02rem] text-brand-primary"
                                >
                                    <CheckCircle
                                        size={18}
                                        weight="fill"
                                        className="shrink-0 text-brand-secondary"
                                    />
                                    <span>{t(key)}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div id="hero-form" className="mx-auto mt-10 max-w-4xl">
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
                                    className="h-12 cursor-pointer bg-brand-secondary px-6 text-white hover:bg-brand-secondary/90 sm:min-w-[190px]"
                                >
                                    {t("landing_generate_report")}
                                    <ArrowRight size={16} className="ml-2" />
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section
                id="report-example"
                className="border-b solid-divider bg-white py-14 md:py-18"
            >
                <div className="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(340px,1fr)_minmax(0,0.95fr)] lg:items-center lg:px-8">
                    <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)] p-4 md:p-6">
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
                                <FilePdf
                                    size={28}
                                    weight="duotone"
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
                    </div>

                    <div className="mx-auto max-w-2xl text-center lg:text-left">
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_example_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem]">
                            {t("landing_example_title")}
                        </h2>
                        <p className="mt-4 text-base leading-[1.7] text-brand-primary md:text-lg">
                            {t("landing_example_desc")}
                        </p>

                        <div className="mt-8 grid gap-3 sm:grid-cols-2">
                            {[
                                {
                                    icon: Scales,
                                    key: "landing_example_feature_1",
                                },
                                {
                                    icon: ChartLineUp,
                                    key: "landing_example_feature_2",
                                },
                                {
                                    icon: MapPin,
                                    key: "landing_example_feature_3",
                                },
                                {
                                    icon: ChartLineUp,
                                    key: "landing_example_feature_4",
                                },
                                {
                                    icon: ShieldCheck,
                                    key: "landing_example_feature_5",
                                },
                            ].map(({ icon: Icon, key }) => (
                                <div
                                    key={key}
                                    className="flex items-start gap-3 border solid-border solid-border-warm bg-white p-4"
                                >
                                    <Icon
                                        size={20}
                                        weight="duotone"
                                        className="mt-0.5 shrink-0 text-brand-secondary"
                                    />
                                    <span className="text-sm text-brand-primary">
                                        {t(key)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <section
                id="problem"
                className={`${warmGradientSectionClass} py-14 md:py-18`}
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-3xl text-center">
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_problem_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem]">
                            {t("landing_problem_title")}
                        </h2>
                    </div>

                    <div className="mt-10 grid gap-5 lg:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)]">
                        <div className="flex h-full flex-col justify-between border border-brand-primary bg-brand-primary p-7 text-white">
                            <div>
                                <p className="text-sm uppercase tracking-[0.18em] text-white/80">
                                    {t("landing_problem_badge")}
                                </p>
                                <p className="mt-8 text-3xl font-bold leading-[1.1] tracking-[-0.03em]">
                                    {t("landing_problem_quote_1")}
                                </p>
                                <p className="mt-3 text-2xl leading-[1.25] text-white/90">
                                    {t("landing_problem_quote_2")}
                                </p>
                            </div>

                            <div className="mt-10 border-t border-white/20 pt-5 text-base leading-[1.7] text-white/85">
                                {t("landing_hero_desc")}
                            </div>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                            {[
                                "landing_problem_item_1",
                                "landing_problem_item_2",
                                "landing_problem_item_3",
                            ].map((key, index) => (
                                <div
                                    key={key}
                                    className="border solid-border solid-border-warm bg-white p-5"
                                >
                                    <div className="mb-4 flex items-center justify-between border-b solid-divider pb-3">
                                        <span className="text-sm font-semibold tracking-[0.16em] text-brand-secondary">
                                            0{index + 1}
                                        </span>
                                        <span className="text-lg font-semibold text-[#d14d3f]">
                                            ×
                                        </span>
                                    </div>
                                    <p className="text-base leading-[1.7] text-brand-primary">
                                        {t(key)}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <section
                id="solution"
                className="border-b solid-divider bg-white py-14 md:py-18"
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-3xl text-center">
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_solution_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem]">
                            {t("landing_solution_title")}
                        </h2>
                    </div>

                    <div className="mt-8 grid gap-4 md:grid-cols-2">
                        {[
                            "landing_solution_item_1",
                            "landing_solution_item_2",
                            "landing_solution_item_3",
                            "landing_solution_item_4",
                        ].map((key) => (
                            <div
                                key={key}
                                className="flex items-start gap-3 border solid-border solid-border-warm bg-[#fcfbf8] p-5"
                            >
                                <CheckCircle
                                    size={20}
                                    weight="fill"
                                    className="mt-0.5 shrink-0 text-brand-secondary"
                                />
                                <span className="text-base leading-relaxed text-brand-primary">
                                    {t(key)}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            <section
                id="how-it-works"
                className="border-b solid-divider bg-white py-14 md:py-18"
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-3xl text-center">
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("how_it_works")}
                        </p>
                        <h2 className="mt-3 text-[2rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem]">
                            {t("landing_how_title")}
                        </h2>
                    </div>

                    <div className="mt-8 grid gap-4 md:grid-cols-3">
                        {[
                            {
                                number: "1",
                                icon: MagnifyingGlass,
                                title: t("landing_step1_title"),
                                desc: t("landing_step1_desc"),
                            },
                            {
                                number: "2",
                                icon: ChartLineUp,
                                title: t("landing_step2_title"),
                                desc: t("landing_step2_desc"),
                            },
                            {
                                number: "3",
                                icon: FilePdf,
                                title: t("landing_step3_title"),
                                desc: t("landing_step3_desc"),
                            },
                        ].map(({ number, icon: Icon, title, desc }) => (
                            <div
                                key={number}
                                className="border solid-border solid-border-warm bg-[#fbfaf8] p-5"
                            >
                                <div className="flex items-center justify-between border-b solid-divider pb-4">
                                    <span className="text-3xl font-bold tracking-[-0.05em] text-brand-primary">
                                        {number}
                                    </span>
                                    <Icon
                                        size={20}
                                        weight="duotone"
                                        className="text-brand-secondary"
                                    />
                                </div>
                                <h3 className="mt-4 text-lg font-semibold text-brand-primary">
                                    {title}
                                </h3>
                                <p className="mt-2 text-sm leading-relaxed text-brand-primary">
                                    {desc}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            <section
                id="pricing"
                className={`${warmGradientSectionClass} py-14 md:py-18`}
            >
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-3xl text-center">
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_pricing_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem]">
                            {t("landing_pricing_title")}
                        </h2>
                        <p className="mt-4 text-base leading-[1.7] text-brand-primary md:text-lg">
                            {t("landing_pricing_desc")}
                        </p>
                    </div>

                    <div className="mt-8 grid gap-4 md:grid-cols-2">
                        {typeOptions.map(
                            ({ type, icon: Icon, labelKey, priceKey }) => (
                                <div
                                    key={type}
                                    className={`border solid-border p-6 ${
                                        type === "buying_living"
                                            ? "border-brand-primary bg-brand-primary text-white"
                                            : "solid-border-warm bg-white"
                                    }`}
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
                                                    weight="duotone"
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
                                </div>
                            ),
                        )}
                    </div>
                </div>
            </section>

            <section
                id="trust"
                className="border-b solid-divider bg-white py-14 md:py-18"
            >
                <div className="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_minmax(300px,0.7fr)] lg:px-8">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary">
                            {t("landing_trust_badge")}
                        </p>
                        <h2 className="mt-3 text-[2rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem]">
                            {t("landing_trust_title")}
                        </h2>

                        <div className="mt-8 space-y-3">
                            {[
                                "landing_trust_item_1",
                                "landing_trust_item_2",
                                "landing_trust_item_3",
                                "landing_trust_item_4",
                            ].map((key) => (
                                <div
                                    key={key}
                                    className="flex items-start gap-3 border solid-border solid-border-warm bg-[#fcfbf8] p-4"
                                >
                                    <CheckCircle
                                        size={18}
                                        weight="fill"
                                        className="mt-0.5 shrink-0 text-brand-secondary"
                                    />
                                    <span className="text-base text-brand-primary">
                                        {t(key)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <aside className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)] p-6 lg:self-start">
                        <p className="text-xs font-semibold uppercase tracking-[0.2em] text-brand-secondary">
                            {t("landing_disclaimer_title")}
                        </p>
                        <p className="mt-3 text-sm leading-relaxed text-brand-primary">
                            {t("landing_disclaimer_desc")}
                        </p>
                    </aside>
                </div>
            </section>

            <section
                id="final-cta"
                className={`${warmGradientSectionClass} py-14 md:py-16`}
            >
                <div className="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-brand-secondary/90">
                        {t("landing_cta_badge")}
                    </p>
                    <h2 className="mt-4 text-[2rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[2.5rem]">
                        {t("landing_cta_title")}
                    </h2>
                    <p className="mt-4 text-base leading-relaxed text-brand-primary md:text-lg">
                        {t("landing_cta_desc")}
                    </p>
                    <a
                        href="#hero-form"
                        className="mt-8 inline-flex items-center gap-2 border solid-border solid-border-warm-strong bg-white px-6 py-3 text-sm font-semibold text-brand-primary transition-colors hover:bg-white/90"
                    >
                        {t("landing_cta_primary")}
                        <ArrowRight size={15} />
                    </a>
                </div>
            </section>
        </PublicLayout>
    );
}
