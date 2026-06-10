import { useState, useEffect, useRef } from "react";
import { Head, router } from "@inertiajs/react";
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
    recaptchaSiteKey?: string | null;
}

export default function SubmitUrl({
    reportType,
    errors,
    recaptchaSiteKey,
}: SubmitUrlProps) {
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

    const submitUrl = (recaptchaToken: string | null = null) => {
        router.post(
            localePath("/validate-url"),
            {
                url,
                report_type: reportType,
                recaptcha_token: recaptchaToken,
            },
            { onFinish: () => setProcessing(false) },
        );
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);

        if (!recaptchaSiteKey) {
            submitUrl();
            return;
        }

        if (!window.grecaptcha) {
            setProcessing(false);
            setClientError(t("contact_validation_recaptcha_failed"));
            return;
        }

        window.grecaptcha.ready(() => {
            window.grecaptcha
                ?.execute(recaptchaSiteKey, { action: "submit_url" })
                .then((token) => submitUrl(token))
                .catch(() => {
                    setProcessing(false);
                    setClientError(t("contact_validation_recaptcha_failed"));
                });
        });
    };

    const sidebar = (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <h3 className="mb-5 text-[0.85rem] font-semibold leading-[1.35] tracking-[-0.02em] text-brand-primary">
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
                                className="mt-0.5 shrink-0 text-brand-secondary"
                            />
                            <p className="text-[14px] leading-[1.65] text-brand-primary/78">
                                {text}
                            </p>
                        </div>
                    ))}
                </div>
            </div>

            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-white">
                        <Clock
                            size={22}
                            weight="fill"
                            className="text-brand-secondary"
                        />
                    </div>
                    <div>
                        <h4 className="mb-1 text-[0.95rem] font-semibold leading-[1.3] text-brand-primary">
                            {t("sidebar_fast_title")}
                        </h4>
                        <p className="text-[14px] leading-[1.65] text-brand-primary/78">
                            {t("sidebar_fast_desc")}
                        </p>
                    </div>
                </div>
            </div>
        </>
    );

    return (
        <PublicLayout>
            <Head title={typeLabels[reportType]}>
                {recaptchaSiteKey && (
                    <script
                        src={`https://www.google.com/recaptcha/api.js?render=${recaptchaSiteKey}`}
                    />
                )}
            </Head>
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
                        className="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-brand-primary/70 transition-colors hover:text-brand-primary"
                    >
                        <ArrowLeft size={14} />
                        {t("back_to_selection")}
                    </a>

                    <h2 className="mb-2 text-[2.1rem] font-bold leading-[0.98] tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">
                        {typeLabels[reportType]}
                    </h2>
                    <p className="mb-6 max-w-2xl text-[14px] leading-[1.68] text-brand-primary/78 md:text-base">
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
                            <button
                                type="submit"
                                disabled={processing || !isValid}
                                className="inline-flex cursor-pointer items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? t("validating") : t("continue")}
                                {!processing && <ArrowRight size={16} />}
                            </button>
                        </div>
                    </form>
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
