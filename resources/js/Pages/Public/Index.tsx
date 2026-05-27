import { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import { ArrowRight, Clock, ShieldCheck } from "@phosphor-icons/react";
import Modal from "@/Components/Modal";
import PublicLayout from "@/Layouts/PublicLayout";
import WizardLayout from "@/Components/WizardLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { landingAssets } from "@/lib/landingAssets";
import { PageProps, ReportType } from "@/types";

const typeOptions: {
    type: ReportType;
    labelKey: string;
    descKey: string;
    imageSrc: string;
}[] = [
    {
        type: "rental_living",
        labelKey: "rental",
        descKey: "rental_living_desc",
        imageSrc: landingAssets.pricingRentalImageSrc,
    },
    {
        type: "buying_living",
        labelKey: "buying",
        descKey: "buying_living_desc",
        imageSrc: landingAssets.pricingBuyingImageSrc,
    },
];

export default function Index() {
    const { appFlags } = usePage<PageProps>().props;
    const [selectedType, setSelectedType] = useState<ReportType | null>(() => {
        if (typeof window === "undefined") {
            return null;
        }

        const type = new URLSearchParams(window.location.search).get("type");

        return type === "buying_living" || type === "rental_living"
            ? type
            : null;
    });
    const [showMaintenanceModal, setShowMaintenanceModal] = useState(false);
    const { t, localePath } = useTranslation();

    const handleContinue = () => {
        if (!selectedType) return;

        if (appFlags.publicWizardMaintenance) {
            setShowMaintenanceModal(true);
            return;
        }

        router.visit(localePath(`/submit-url?type=${selectedType}`));
    };

    const sidebar = (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <h3 className="mb-5 text-[0.85rem] font-semibold leading-[1.35] tracking-[-0.02em] text-brand-primary">
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
                            <p className="text-[14px] leading-[1.65] text-brand-primary/78">
                                {text}
                            </p>
                        </div>
                    ))}
                </div>
            </div>

            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 items-center justify-center bg-white">
                        <ShieldCheck
                            size={20}
                            weight="fill"
                            className="text-brand-secondary"
                        />
                    </div>
                    <div>
                        <h4 className="mb-1 text-[0.95rem] font-semibold leading-[1.3] text-brand-primary">
                            {t("sidebar_secure_title")}
                        </h4>
                        <p className="text-[14px] leading-[1.65] text-brand-primary/78">
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
            <Modal
                show={showMaintenanceModal}
                onClose={() => setShowMaintenanceModal(false)}
                maxWidth="2xl"
                centered
                panelClassName="rounded-none"
            >
                <div className="overflow-hidden border border-brand-primary/10 bg-[linear-gradient(180deg,#fffaf4_0%,#ffffff_42%,#eef1ff_100%)]">
                    <div className="h-1 bg-brand-secondary" />
                    <div className="flex flex-col items-center px-6 py-8 text-center sm:px-10 sm:py-10">
                        <div className="flex h-12 w-12 items-center justify-center bg-brand-primary text-white shadow-[0_12px_30px_rgba(18,35,74,0.16)]">
                            <Clock size={22} weight="fill" />
                        </div>

                        <div className="mt-5 max-w-xl">
                            <h3 className="text-[1.9rem] font-bold leading-[0.98] tracking-[-0.04em] text-brand-primary sm:text-[2.3rem]">
                                {t("wizard_maintenance_title")}
                            </h3>
                            <p className="mt-3 text-[14px] leading-[1.75] text-brand-primary/78 sm:text-base">
                                {t("wizard_maintenance_body")}
                            </p>
                        </div>

                        <div className="mt-7 flex justify-center">
                            <button
                                type="button"
                                onClick={() => setShowMaintenanceModal(false)}
                                className="inline-flex items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92"
                            >
                                {t("wizard_maintenance_close")}
                                <ArrowRight size={16} />
                            </button>
                        </div>
                    </div>
                </div>
            </Modal>
            <WizardLayout
                currentStep={1}
                sidebar={sidebar}
                reportType={selectedType}
            >
                <div>
                    <h2 className="mb-2 text-[2.1rem] font-bold leading-[0.98] tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">
                        {t("select_report_type")}
                    </h2>
                    <p className="mb-8 max-w-2xl text-[14px] leading-[1.68] text-brand-primary/78 md:text-base">
                        {t("what_report")}
                    </p>

                    <div className="space-y-4">
                        {typeOptions.map(
                            ({ type, labelKey, descKey, imageSrc }) => {
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
                                            <div className="relative hidden h-28 w-32 shrink-0 overflow-hidden md:block">
                                                <div className="absolute inset-x-2 bottom-2 h-8 rounded-full bg-brand-primary/12 blur-2xl" />
                                                <img
                                                    src={imageSrc}
                                                    alt={t(labelKey)}
                                                    className="relative z-10 h-full w-full scale-[1.14] object-contain"
                                                />
                                            </div>
                                        </div>
                                    </button>
                                );
                            },
                        )}
                    </div>

                    <div className="mt-8 flex justify-end">
                        <button
                            type="button"
                            onClick={handleContinue}
                            disabled={!selectedType}
                            className="inline-flex cursor-pointer items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {t("continue")}
                            <ArrowRight size={16} />
                        </button>
                    </div>
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
