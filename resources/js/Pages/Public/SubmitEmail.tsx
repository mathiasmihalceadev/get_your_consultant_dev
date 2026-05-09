import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
    ArrowRight,
    Envelope,
    ShieldCheck,
    FilePdf,
} from "@phosphor-icons/react";
import { Report, ReportType } from "@/types";
import PublicLayout from "@/Layouts/PublicLayout";
import WizardLayout from "@/Components/WizardLayout";
import { useTranslation } from "@/hooks/useTranslation";

function BlurredReportPreview() {
    return (
        <div className="relative select-none pointer-events-none">
            <div
                className="border solid-border solid-border-warm bg-white p-5 space-y-4"
                style={{ filter: "blur(4px)" }}
            >
                {/* Header */}
                <div className="border-b-2 border-brand-primary pb-3">
                    <div className="w-28 h-4 bg-brand-primary rounded mb-2" />
                    <div className="w-48 h-5 bg-gray-300 rounded" />
                    <div className="w-36 h-3 bg-gray-200 rounded mt-2" />
                </div>

                {/* Property Summary */}
                <div>
                    <div className="w-32 h-4 bg-brand-primary/60 rounded mb-3" />
                    <div className="grid grid-cols-2 gap-2">
                        {[...Array(6)].map((_, i) => (
                            <div key={i} className="space-y-1">
                                <div className="w-16 h-2.5 bg-gray-200 rounded" />
                                <div className="w-24 h-3 bg-gray-300 rounded" />
                            </div>
                        ))}
                    </div>
                </div>

                {/* Score section */}
                <div>
                    <div className="w-28 h-4 bg-brand-primary/60 rounded mb-3" />
                    <div className="grid grid-cols-2 gap-2">
                        {[...Array(4)].map((_, i) => (
                            <div key={i} className="space-y-1">
                                <div className="w-20 h-2.5 bg-gray-200 rounded" />
                                <div className="w-8 h-6 bg-green-300 rounded" />
                            </div>
                        ))}
                    </div>
                </div>

                {/* Final score */}
                <div className="border-[1.5px] border-brand-primary/35 rounded p-3 text-center">
                    <div className="w-12 h-8 bg-brand-primary/40 rounded mx-auto mb-1" />
                    <div className="w-20 h-3 bg-gray-200 rounded mx-auto" />
                    <div className="w-16 h-5 bg-green-300 rounded mx-auto mt-2" />
                </div>
            </div>

            {/* Overlay icon */}
            <div className="absolute inset-0 flex items-center justify-center">
                <div className="flex items-center gap-2 border solid-border solid-border-warm-strong bg-white px-5 py-3">
                    <FilePdf
                        size={24}
                        weight="fill"
                        className="text-brand-secondary"
                    />
                    <span className="text-sm font-semibold text-brand-primary">
                        PDF
                    </span>
                </div>
            </div>
        </div>
    );
}

interface SubmitEmailProps {
    report: Pick<Report, "id" | "url" | "report_type">;
    errors: Record<string, string>;
}

export default function SubmitEmail({ report, errors }: SubmitEmailProps) {
    const { t, localePath } = useTranslation();
    const [email, setEmail] = useState("");
    const [processing, setProcessing] = useState(false);

    const typeLabels: Record<ReportType, string> = {
        rental_living: t("rental"),
        rental_business: t("type_rental_business"),
        buying_living: t("buying"),
        buying_business: t("type_buying_business"),
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post(
            localePath("/submit-email"),
            { email, report_id: report.id },
            { onFinish: () => setProcessing(false) },
        );
    };

    const sidebar = (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 flex-shrink-0 items-center justify-center bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]">
                        <Envelope
                            size={22}
                            weight="fill"
                            className="text-brand-tertiary"
                        />
                    </div>
                    <div>
                        <h4 className="text-sm font-bold text-brand-primary mb-1">
                            {t("sidebar_fast_title")}
                        </h4>
                        <p className="text-sm leading-[1.7] text-brand-primary">
                            {t("sidebar_fast_desc")}
                        </p>
                    </div>
                </div>
            </div>

            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 flex-shrink-0 items-center justify-center bg-white">
                        <ShieldCheck
                            size={22}
                            weight="fill"
                            className="text-brand-secondary"
                        />
                    </div>
                    <div>
                        <h4 className="text-sm font-bold text-brand-primary mb-1">
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
            <Head title={t("your_email")} />
            <WizardLayout
                currentStep={3}
                sidebar={sidebar}
                reportType={report.report_type}
            >
                <div>
                    <p className="mb-3 text-sm font-semibold text-brand-secondary">
                        {t("wizard_step_email")}
                    </p>
                    <h2 className="mb-2 text-[2rem] font-bold tracking-[-0.035em] text-brand-primary md:text-[2.45rem]">
                        {t("almost_there")}
                    </h2>

                    <div className="flex flex-wrap gap-2 mb-1">
                        <span className="inline-flex items-center px-2.5 py-0.5 bg-brand-primary/10 text-brand-primary text-xs font-semibold">
                            {typeLabels[report.report_type]}
                        </span>
                    </div>
                    <p className="mb-6 break-all text-sm leading-[1.7] text-brand-primary">
                        {report.url}
                    </p>

                    {/* Blurred PDF preview */}
                    <div className="mb-6">
                        <BlurredReportPreview />
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <Label
                                htmlFor="email"
                                className="text-sm font-medium mb-1.5 block"
                            >
                                {t("your_email")}
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                placeholder={t("email_placeholder")}
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                                className={`solid-border text-brand-primary placeholder:text-brand-primary/55 ${
                                    errors?.email
                                        ? "border-red-500"
                                        : "border-brand-primary/30"
                                }`}
                            />
                            {errors?.email && (
                                <p className="text-sm text-red-600 mt-1.5">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div className="flex justify-end">
                            <Button
                                type="submit"
                                disabled={processing || !email}
                                className="bg-brand-secondary hover:bg-brand-secondary/90 text-white px-6 cursor-pointer"
                            >
                                {processing
                                    ? t("submitting")
                                    : t("get_my_report")}
                                {!processing && (
                                    <ArrowRight size={16} className="ml-2" />
                                )}
                            </Button>
                        </div>
                    </form>

                    <p className="mt-6 text-sm leading-[1.7] text-brand-primary">
                        {t("report_delivery_note")}
                    </p>
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
