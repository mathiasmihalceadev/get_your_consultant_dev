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
        rental_living: t("type_rental_living"),
        rental_business: t("type_rental_business"),
        buying_living: t("type_buying_living"),
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
        <div className="space-y-6">
            <div className="bg-gray-50 border border-gray-200 p-6">
                <h3 className="text-xs font-bold text-brand-primary uppercase tracking-widest mb-5">
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
                                className="text-brand-secondary flex-shrink-0 mt-0.5"
                            />
                            <p className="text-sm text-brand-neutral">{text}</p>
                        </div>
                    ))}
                </div>
            </div>

            <div className="border border-gray-200 p-6">
                <div className="flex items-start gap-3">
                    <div className="w-10 h-10 bg-brand-secondary/10 flex items-center justify-center flex-shrink-0">
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
                        <p className="text-xs text-brand-neutral leading-relaxed">
                            {t("sidebar_fast_desc")}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );

    return (
        <PublicLayout>
            <Head title={typeLabels[reportType]} />
            <WizardLayout currentStep={2} sidebar={sidebar}>
                <div>
                    <a
                        href={localePath("/")}
                        onClick={(e) => {
                            e.preventDefault();
                            router.visit(localePath("/"));
                        }}
                        className="inline-flex items-center gap-1 text-xs font-medium text-brand-neutral hover:text-brand-primary mb-6 transition-colors uppercase tracking-wider"
                    >
                        <ArrowLeft size={14} />
                        {t("back_to_selection")}
                    </a>

                    <h2 className="text-2xl md:text-3xl font-bold text-brand-primary mb-1 tracking-tight">
                        {typeLabels[reportType]}
                    </h2>
                    <p className="text-brand-neutral mb-6">{t("enter_url")}</p>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <Label
                                htmlFor="url"
                                className="text-sm font-medium mb-1.5 block"
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
                                className={
                                    urlError
                                        ? "border-red-500"
                                        : isValid
                                          ? "border-green-500"
                                          : ""
                                }
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
