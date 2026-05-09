import { useState, useEffect, useRef } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
    ArrowRight,
    ArrowLeft,
    CheckCircle,
    Clock,
} from "@phosphor-icons/react";
import { ReportType } from "@/types";
import PublicLayout from "@/Layouts/PublicLayout";
import WizardLayout from "@/Components/WizardLayout";
import { useTranslation } from "@/hooks/useTranslation";

function isValidUrl(value: string): boolean {
    try {
        const parsed = new URL(value);
        return parsed.protocol === "http:" || parsed.protocol === "https:";
    } catch {
        return false;
    }
}

interface SubmitUrlProps {
    reportType: ReportType;
    errors: Record<string, string>;
}

export default function SubmitUrl({ reportType, errors }: SubmitUrlProps) {
    const { t, localePath } = useTranslation();
    const [url, setUrl] = useState("");
    const [processing, setProcessing] = useState(false);
    const [touched, setTouched] = useState(false);
    const [clientError, setClientError] = useState<string | null>(null);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const typeLabels: Record<ReportType, string> = {
        rental_living: t("rental"),
        rental_business: t("type_rental_business"),
        buying_living: t("buying"),
        buying_business: t("type_buying_business"),
    };

    useEffect(() => {
        if (!touched) return;

        if (debounceRef.current) clearTimeout(debounceRef.current);

        debounceRef.current = setTimeout(() => {
            if (!url.trim()) {
                setClientError(t("url_required"));
            } else if (!isValidUrl(url)) {
                setClientError(t("url_invalid"));
            } else {
                setClientError(null);
            }
        }, 400);

        return () => {
            if (debounceRef.current) clearTimeout(debounceRef.current);
        };
    }, [url, touched]);

    const urlError = clientError || errors?.url || null;
    const isValid = touched && !clientError && url.trim().length > 0;

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post(
            localePath("/validate-url"),
            { url, report_type: reportType },
            { onFinish: () => setProcessing(false) },
        );
    };

    const sidebar = (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <h3 className="mb-5 text-xs font-bold uppercase tracking-tightcaps text-brand-secondary">
                    {t("sidebar_report_title")}
                </h3>
                <div className="space-y-3">
                    {[
                        t("sidebar_report_1"),
                        t("sidebar_report_2"),
                        t("sidebar_report_3"),
                        t("sidebar_report_4"),
                    ].map((text, i) => (
                        <div key={i} className="flex items-start gap-3">
                            <CheckCircle
                                size={18}
                                weight="fill"
                                className="mt-0.5 flex-shrink-0 text-brand-secondary"
                            />
                            <p className="text-sm leading-[1.7] text-brand-primary">
                                {text}
                            </p>
                        </div>
                    ))}
                </div>
            </div>

            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 flex-shrink-0 items-center justify-center bg-white">
                        <Clock
                            size={22}
                            weight="fill"
                            className="text-brand-secondary"
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
        </>
    );

    return (
        <PublicLayout>
            <Head title={typeLabels[reportType]} />
            <WizardLayout
                currentStep={2}
                sidebar={sidebar}
                reportType={reportType}
            >
                <div>
                    <a
                        href={localePath("/get-report")}
                        onClick={(e) => {
                            e.preventDefault();
                            router.visit(localePath("/get-report"));
                        }}
                        className="mb-6 inline-flex items-center gap-1 text-xs font-medium uppercase tracking-tightcaps text-brand-primary/70 transition-colors hover:text-brand-primary"
                    >
                        <ArrowLeft size={14} />
                        {t("back_to_selection")}
                    </a>

                    <p className="mb-3 text-sm font-semibold text-brand-secondary">
                        {t("wizard_step_property")}
                    </p>
                    <h2 className="mb-2 text-[2rem] font-bold tracking-[-0.035em] text-brand-primary md:text-[2.45rem]">
                        {typeLabels[reportType]}
                    </h2>
                    <p className="mb-6 max-w-2xl text-base leading-[1.7] text-brand-primary">
                        {t("enter_url")}
                    </p>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <Label
                                htmlFor="url"
                                className="mb-1.5 block text-sm font-semibold text-brand-primary"
                            >
                                {t("property_url_label")}
                            </Label>
                            <Input
                                id="url"
                                placeholder={t("url_placeholder")}
                                value={url}
                                onChange={(e) => {
                                    setUrl(e.target.value);
                                    if (!touched) setTouched(true);
                                }}
                                onBlur={() => setTouched(true)}
                                required
                                className={`solid-border text-brand-primary placeholder:text-brand-primary/55 ${
                                    urlError
                                        ? "border-red-500"
                                        : isValid
                                          ? "border-green-500"
                                          : "border-brand-primary/30"
                                }`}
                            />
                            {urlError && (
                                <p className="text-sm text-red-600 mt-1.5">
                                    {urlError}
                                </p>
                            )}
                        </div>

                        <div className="flex justify-end">
                            <Button
                                type="submit"
                                disabled={processing || !isValid}
                                className="bg-brand-secondary hover:bg-brand-secondary/90 text-white px-6 cursor-pointer"
                            >
                                {processing ? t("validating") : t("continue")}
                                {!processing && (
                                    <ArrowRight size={16} className="ml-2" />
                                )}
                            </Button>
                        </div>
                    </form>
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
