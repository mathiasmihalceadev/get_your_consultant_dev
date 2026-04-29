import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import {
    Key,
    House,
    ArrowRight,
    CheckCircle,
    Icon,
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
        labelKey: "type_rental_living",
        descKey: "rental_living_desc",
    },
    {
        type: "buying_living",
        icon: House,
        labelKey: "type_buying_living",
        descKey: "buying_living_desc",
    },
];

export default function Index() {
    const [selectedType, setSelectedType] = useState<ReportType | null>(null);
    const { t, localePath } = useTranslation();

    const handleContinue = () => {
        if (!selectedType) return;
        router.visit(localePath(`/submit-url?type=${selectedType}`));
    };

    const sidebar = (
        <div className="space-y-6">
            <div className="bg-gray-50 border border-gray-200 p-6">
                <h3 className="text-xs font-bold text-brand-primary uppercase tracking-widest mb-5">
                    {t("sidebar_how_title")}
                </h3>
                <div className="space-y-4">
                    {[
                        t("sidebar_how_step_1"),
                        t("sidebar_how_step_2"),
                        t("sidebar_how_step_3"),
                    ].map((text, i) => (
                        <div key={i} className="flex items-start gap-3">
                            <div className="w-6 h-6 bg-brand-secondary flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span className="text-white text-xs font-bold">
                                    {i + 1}
                                </span>
                            </div>
                            <p className="text-sm text-brand-neutral leading-relaxed">
                                {text}
                            </p>
                        </div>
                    ))}
                </div>
            </div>

            <div className="border border-gray-200 p-6">
                <div className="flex items-start gap-3">
                    <div className="w-10 h-10 bg-brand-tertiary/10 flex items-center justify-center flex-shrink-0">
                        <CheckCircle
                            size={22}
                            weight="fill"
                            className="text-brand-tertiary"
                        />
                    </div>
                    <div>
                        <h4 className="text-sm font-bold text-brand-primary mb-1">
                            {t("sidebar_secure_title")}
                        </h4>
                        <p className="text-xs text-brand-neutral leading-relaxed">
                            {t("sidebar_secure_desc")}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );

    return (
        <PublicLayout>
            <Head title={t("home_title")} />
            <WizardLayout currentStep={1} sidebar={sidebar}>
                <div>
                    <h2 className="text-2xl md:text-3xl font-bold text-brand-primary mb-1 tracking-tight">
                        {t("select_report_type")}
                    </h2>
                    <p className="text-brand-neutral mb-6">
                        {t("what_report")}
                    </p>

                    <div className="space-y-3 mb-8">
                        {typeOptions.map(
                            ({ type, icon: TypeIcon, labelKey, descKey }) => (
                                <button
                                    key={type}
                                    onClick={() => setSelectedType(type)}
                                    className={`w-full flex items-center gap-4 p-4 border-2 text-left transition-all cursor-pointer ${
                                        selectedType === type
                                            ? "border-brand-tertiary bg-brand-tertiary/5"
                                            : "border-gray-200 hover:border-gray-300 bg-white"
                                    }`}
                                >
                                    <div
                                        className={`w-11 h-11 flex items-center justify-center flex-shrink-0 ${
                                            selectedType === type
                                                ? "bg-brand-tertiary/10"
                                                : "bg-gray-50"
                                        }`}
                                    >
                                        <TypeIcon
                                            size={22}
                                            weight="duotone"
                                            className={
                                                selectedType === type
                                                    ? "text-brand-tertiary"
                                                    : "text-brand-primary"
                                            }
                                        />
                                    </div>
                                    <div>
                                        <p className="font-semibold text-brand-primary text-sm">
                                            {t(labelKey)}
                                        </p>
                                        <p className="text-xs text-brand-neutral mt-0.5">
                                            {t(descKey)}
                                        </p>
                                    </div>
                                </button>
                            ),
                        )}
                    </div>

                    <div className="flex justify-end">
                        <Button
                            onClick={handleContinue}
                            disabled={!selectedType}
                            className="bg-brand-secondary hover:bg-brand-secondary/90 text-white px-6 cursor-pointer"
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
