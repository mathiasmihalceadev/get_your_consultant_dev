import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import {
    Key,
    House,
    ArrowRight,
    Icon,
    CheckCircle,
    ShieldCheck,
} from "@phosphor-icons/react";
import PublicLayout from "@/Layouts/PublicLayout";
import WizardLayout from "@/Components/WizardLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { ReportType } from "@/types";

const typeOptions: {
    type: ReportType;
    icon: Icon;
    labelKey: string;
    descKey: string;
}[] = [
    {
        type: "rental_living",
        icon: Key,
        labelKey: "rental",
        descKey: "rental_living_desc",
    },
    {
        type: "buying_living",
        icon: House,
        labelKey: "buying",
        descKey: "buying_living_desc",
    },
];

export default function Index() {
    const [selectedType, setSelectedType] = useState<ReportType | null>(() => {
        if (typeof window === "undefined") {
            return null;
        }

        const type = new URLSearchParams(window.location.search).get("type");

        return type === "buying_living" || type === "rental_living"
            ? type
            : null;
    });
    const { t, localePath } = useTranslation();

    const handleContinue = () => {
        if (!selectedType) return;
        router.visit(localePath(`/submit-url?type=${selectedType}`));
    };

    const sidebar = (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <h3 className="mb-5 text-xs font-bold uppercase tracking-widest text-brand-secondary">
                    {t("sidebar_how_title")}
                </h3>
                <div className="space-y-4">
                    {[
                        t("sidebar_how_step_1"),
                        t("sidebar_how_step_2"),
                        t("sidebar_how_step_3"),
                    ].map((text, i) => (
                        <div key={i} className="flex items-start gap-3">
                            <div className="flex h-7 w-7 items-center justify-center bg-brand-primary text-xs font-semibold text-white">
                                {i + 1}
                            </div>
                            <p className="text-sm leading-[1.7] text-brand-primary">
                                {text}
                            </p>
                        </div>
                    ))}
                </div>
            </div>

            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)] p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 items-center justify-center bg-white">
                        <ShieldCheck
                            size={20}
                            weight="fill"
                            className="text-brand-secondary"
                        />
                    </div>
                    <div>
                        <h4 className="mb-1 text-sm font-bold text-brand-primary">
                            {t("sidebar_secure_title")}
                        </h4>
                        <p className="text-sm leading-[1.7] text-brand-primary">
                            {t("sidebar_secure_desc")}
                        </p>
                    </div>
                </div>
            </div>
        </>
    );

    return (
        <PublicLayout>
            <Head title={t("home_title")} />
            <WizardLayout currentStep={1} sidebar={sidebar}>
                <div>
                    <p className="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-brand-secondary">
                        {t("landing_badge")}
                    </p>
                    <h2 className="mb-2 text-[2.1rem] font-bold tracking-[-0.035em] text-brand-primary md:text-[2.6rem]">
                        {t("select_report_type")}
                    </h2>
                    <p className="mb-8 max-w-2xl text-base leading-[1.7] text-brand-primary">
                        {t("what_report")}
                    </p>

                    <div className="space-y-4">
                        {typeOptions.map(
                            ({ type, icon: TypeIcon, labelKey, descKey }) => {
                                const isActive = selectedType === type;

                                return (
                                    <button
                                        key={type}
                                        onClick={() => setSelectedType(type)}
                                        className={`w-full cursor-pointer border solid-border p-5 text-left transition-colors ${
                                            isActive
                                                ? "border-brand-primary bg-brand-primary text-white"
                                                : "solid-border-warm bg-white text-brand-primary hover:border-brand-primary"
                                        }`}
                                    >
                                        <div className="flex items-start gap-4">
                                            <div
                                                className={`flex h-11 w-11 items-center justify-center ${
                                                    isActive
                                                        ? "bg-white/10"
                                                        : "bg-brand-primary/5"
                                                }`}
                                            >
                                                <TypeIcon
                                                    size={22}
                                                    weight="duotone"
                                                    className={
                                                        isActive
                                                            ? "text-white"
                                                            : "text-brand-secondary"
                                                    }
                                                />
                                            </div>
                                            <div className="flex-1">
                                                <p className="text-lg font-semibold">
                                                    {t(labelKey)}
                                                </p>
                                                <p
                                                    className={`mt-2 text-sm leading-[1.7] ${
                                                        isActive
                                                            ? "text-white/85"
                                                            : "text-brand-primary"
                                                    }`}
                                                >
                                                    {t(descKey)}
                                                </p>
                                            </div>
                                        </div>
                                    </button>
                                );
                            },
                        )}
                    </div>

                    <div className="mt-8 flex justify-end">
                        <Button
                            onClick={handleContinue}
                            disabled={!selectedType}
                            className="bg-brand-secondary px-6 text-white hover:bg-brand-secondary/90"
                        >
                            {t("continue")}
                            <ArrowRight size={16} className="ml-2" />
                        </Button>
                    </div>
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
