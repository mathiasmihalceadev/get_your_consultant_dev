import { useState } from "react";
import { Head, router } from "@inertiajs/react";
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
    pricing: {
        base_currency: string;
        base_amount_minor: number;
        checkout_currency: string;
        checkout_amount_minor: number;
    } | null;
    errors: Record<string, string>;
}

const campaignRegularPrices = {
    ro: {
        buying_living: "27,99 €",
        rental_living: "17,99 €",
    },
    en: {
        buying_living: "€27.99",
        rental_living: "€17.99",
    },
} satisfies Record<
    "ro" | "en",
    Partial<Record<ReportType, string>>
>;

export default function SubmitEmail({
    report,
    pricing,
    errors,
}: SubmitEmailProps) {
    const { t, locale, localePath } = useTranslation();
    const [email, setEmail] = useState("");
    const [emailConfirmation, setEmailConfirmation] = useState("");
    const [acceptTerms, setAcceptTerms] = useState(false);
    const [processing, setProcessing] = useState(false);
    const termsPath =
        locale === "ro" ? "/termeni-si-conditii" : "/terms-and-conditions";
    const regularPrice =
        campaignRegularPrices[locale === "ro" ? "ro" : "en"][
            report.report_type
        ];

    const formatMoney = (minor: number, currency: string) =>
        new Intl.NumberFormat(locale === "ro" ? "ro-RO" : "en-IE", {
            style: "currency",
            currency: currency.toUpperCase(),
            currencyDisplay:
                currency.toLowerCase() === "ron" ? "code" : "narrowSymbol",
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(minor / 100);

    const launchPrice = pricing
        ? formatMoney(pricing.base_amount_minor, pricing.base_currency)
        : null;
    const checkoutPrice = pricing
        ? formatMoney(pricing.checkout_amount_minor, pricing.checkout_currency)
        : null;
    const showCheckoutCurrency =
        pricing &&
        pricing.checkout_currency.toLowerCase() !==
            pricing.base_currency.toLowerCase();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post(
            localePath("/submit-email"),
            {
                email,
                email_confirmation: emailConfirmation,
                report_id: report.id,
                accept_terms: acceptTerms,
            },
            { onFinish: () => setProcessing(false) },
        );
    };

    const emailsMatch =
        email.trim().length > 0 &&
        emailConfirmation.trim().length > 0 &&
        email.trim().toLowerCase() === emailConfirmation.trim().toLowerCase();

    const sidebar = (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]">
                        <Envelope
                            size={22}
                            weight="fill"
                            className="text-brand-tertiary"
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

            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-white">
                        <ShieldCheck
                            size={22}
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
            <Head title={t("your_email")} />
            <WizardLayout
                currentStep={3}
                sidebar={sidebar}
                reportType={report.report_type}
            >
                <div>
                    <h2 className="mb-2 text-[2.1rem] font-bold leading-[0.98] tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">
                        {t("almost_there")}
                    </h2>

                    <p className="mb-6 break-all text-[14px] leading-[1.68] text-brand-primary/78">
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

                        <div>
                            <Label
                                htmlFor="email_confirmation"
                                className="text-sm font-medium mb-1.5 block"
                            >
                                {t("your_email_confirmation")}
                            </Label>
                            <Input
                                id="email_confirmation"
                                type="email"
                                placeholder={t("email_placeholder")}
                                value={emailConfirmation}
                                onChange={(e) =>
                                    setEmailConfirmation(e.target.value)
                                }
                                required
                                className={`solid-border text-brand-primary placeholder:text-brand-primary/55 ${
                                    errors?.email_confirmation ||
                                    (emailConfirmation && !emailsMatch)
                                        ? "border-red-500"
                                        : "border-brand-primary/30"
                                }`}
                            />
                            {errors?.email_confirmation ? (
                                <p className="text-sm text-red-600 mt-1.5">
                                    {errors.email_confirmation}
                                </p>
                            ) : (
                                emailConfirmation &&
                                !emailsMatch && (
                                    <p className="text-sm text-red-600 mt-1.5">
                                        {t("wizard_email_confirmation_mismatch")}
                                    </p>
                                )
                            )}
                        </div>

                        {pricing && launchPrice && (
                            <div className="border border-brand-primary/10 bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] px-5 py-4 text-brand-primary shadow-[0_12px_30px_rgba(52,48,106,0.07)]">
                                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p className="text-sm font-semibold text-brand-primary/52">
                                            {t("landing_price_normal_label")}
                                        </p>
                                        {regularPrice && (
                                            <p className="mt-1 text-lg font-semibold leading-none text-brand-primary/42 line-through decoration-2 decoration-brand-primary/45">
                                                {regularPrice}
                                            </p>
                                        )}
                                        <p className="mt-3 text-base font-semibold leading-[1.35] text-[#b86f12]">
                                            {t("landing_price_launch_label")}
                                        </p>
                                    </div>

                                    <div className="sm:text-right">
                                        <p className="text-3xl font-bold tracking-[-0.05em] text-brand-primary">
                                            {launchPrice}
                                        </p>
                                        <p className="mt-1 text-xs font-semibold text-brand-primary/54">
                                            {t("landing_price_vat_included")}
                                        </p>
                                        {showCheckoutCurrency && (
                                            <p className="mt-2 text-[13px] font-semibold text-brand-primary/62">
                                                {t(
                                                    "submit_email_checkout_amount_label",
                                                )}
                                                : {checkoutPrice}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <p className="mt-3 text-[13px] font-semibold text-brand-primary/60">
                                    {t("landing_price_campaign_note")}
                                </p>
                            </div>
                        )}

                        <div>
                            <label className="flex cursor-pointer items-start gap-3 border border-brand-primary/10 bg-brand-primary/3 px-4 py-3 text-sm leading-6 text-brand-primary/76">
                                <input
                                    id="accept_terms"
                                    type="checkbox"
                                    checked={acceptTerms}
                                    onChange={(e) =>
                                        setAcceptTerms(e.target.checked)
                                    }
                                    required
                                    className="mt-1 h-4 w-4 shrink-0 cursor-pointer border-brand-primary/25 text-brand-primary"
                                />
                                <span>
                                    {t("wizard_accept_terms_intro")}
                                    <a
                                        href={termsPath}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="font-semibold text-brand-secondary underline decoration-brand-secondary/35 underline-offset-3 hover:text-brand-primary"
                                    >
                                        {t("wizard_terms_link")}
                                    </a>
                                    {t("wizard_accept_terms_outro")}
                                </span>
                            </label>
                            {errors?.accept_terms && (
                                <p className="mt-1.5 text-sm text-red-600">
                                    {errors.accept_terms}
                                </p>
                            )}
                        </div>

                        <div className="flex justify-end">
                            <button
                                type="submit"
                                disabled={
                                    processing || !emailsMatch || !acceptTerms
                                }
                                className="inline-flex cursor-pointer items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing
                                    ? t("submitting")
                                    : t("continue_to_payment")}
                                {!processing && <ArrowRight size={16} />}
                            </button>
                        </div>
                    </form>

                    {/* <p className="mt-6 text-[14px] leading-[1.68] text-brand-primary/78">
                        {t("payment_checkout_note")}
                    </p> */}
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
