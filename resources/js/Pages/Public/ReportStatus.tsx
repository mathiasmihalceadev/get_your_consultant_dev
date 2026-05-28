import { useEffect, useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import {
    ArrowClockwise,
    ChartBar,
    CheckCircle,
    CreditCard,
    FilePdf,
    MapPin,
    WarningCircle,
} from "@phosphor-icons/react";
import { PageProps, Report, ReportStatus as ReportStatusValue } from "@/types";
import PublicLayout from "@/Layouts/PublicLayout";
import WizardLayout from "@/Components/WizardLayout";
import { useTranslation } from "@/hooks/useTranslation";

function GeneratingAnimation() {
    return (
        <div className="flex items-center justify-center gap-3 py-8">
            {[0, 1, 2, 3, 4].map((i) => (
                <div
                    key={i}
                    className="w-4 h-4 rounded-full"
                    style={{
                        backgroundColor:
                            i % 3 === 0
                                ? "#34306A"
                                : i % 3 === 1
                                  ? "#4E59B7"
                                  : "#7380D9",
                        animation: `bubbleBounce 1.4s ease-in-out ${i * 0.2}s infinite`,
                    }}
                />
            ))}
            <style>{`
                @keyframes bubbleBounce {
                    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
                    40% { transform: scale(1.2); opacity: 1; }
                }
            `}</style>
        </div>
    );
}

function CompletedAnimation() {
    return (
        <div className="relative w-32 h-32 mx-auto">
            <div className="absolute inset-0 bg-green-50 rounded-full" />
            <div className="absolute inset-0 flex items-center justify-center">
                <CheckCircle
                    size={64}
                    weight="fill"
                    className="text-green-600"
                />
            </div>
        </div>
    );
}

function PaymentAnimation() {
    return (
        <div className="relative mx-auto h-32 w-32">
            <div className="absolute inset-0 rounded-full bg-amber-50" />
            <div className="absolute inset-0 flex items-center justify-center">
                <CreditCard
                    size={56}
                    weight="fill"
                    className="text-amber-600"
                />
            </div>
        </div>
    );
}

function AlertAnimation() {
    return (
        <div className="relative mx-auto h-32 w-32">
            <div className="absolute inset-0 rounded-full bg-red-50" />
            <div className="absolute inset-0 flex items-center justify-center">
                <WarningCircle
                    size={56}
                    weight="fill"
                    className="text-red-600"
                />
            </div>
        </div>
    );
}

const pollingStatuses: ReportStatusValue[] = [
    "awaiting_payment",
    "payment_processing",
    "pending",
];

const retryablePaymentStatuses: ReportStatusValue[] = [
    "awaiting_payment",
    "payment_cancelled",
    "payment_failed",
];

const paymentConfirmedStatuses: ReportStatusValue[] = [
    "payment_processing",
    "pending",
    "to_be_sent",
    "sent",
    "error",
];

const generationStartedStatuses: ReportStatusValue[] = [
    "pending",
    "to_be_sent",
    "sent",
    "error",
];

const statusConfig: Record<
    ReportStatusValue,
    {
        animation: "complete" | "payment" | "processing" | "alert";
        colorClass: string;
        messageKey: string;
    }
> = {
    not_accessible: {
        animation: "alert",
        colorClass: "text-red-600",
        messageKey: "status_not_accessible",
    },
    awaiting_payment: {
        animation: "payment",
        colorClass: "text-amber-600",
        messageKey: "status_awaiting_payment",
    },
    payment_processing: {
        animation: "processing",
        colorClass: "text-brand-tertiary",
        messageKey: "status_payment_processing",
    },
    payment_cancelled: {
        animation: "alert",
        colorClass: "text-amber-600",
        messageKey: "status_payment_cancelled",
    },
    payment_failed: {
        animation: "alert",
        colorClass: "text-red-600",
        messageKey: "status_payment_failed",
    },
    pending: {
        animation: "processing",
        colorClass: "text-brand-tertiary",
        messageKey: "status_pending",
    },
    to_be_sent: {
        animation: "complete",
        colorClass: "text-green-600",
        messageKey: "status_to_be_sent",
    },
    sent: {
        animation: "complete",
        colorClass: "text-green-600",
        messageKey: "status_sent",
    },
    error: {
        animation: "alert",
        colorClass: "text-red-600",
        messageKey: "status_error",
    },
};

interface ReportStatusProps {
    report: Report;
    pageToken: string;
}

export default function ReportStatus({
    report: initialReport,
    pageToken,
}: ReportStatusProps) {
    const { t, localePath } = useTranslation();
    const { flash } = usePage<PageProps>().props;
    const [reportData, setReportData] = useState<Report>(initialReport);
    const [retrying, setRetrying] = useState(false);

    const currentStatus = statusConfig[reportData.status];
    const isCompleted =
        reportData.status === "sent" || reportData.status === "to_be_sent";
    const shouldPoll = pollingStatuses.includes(reportData.status);
    const canRetryPayment = retryablePaymentStatuses.includes(
        reportData.status,
    );

    useEffect(() => {
        if (!shouldPoll) {
            return;
        }

        const interval = setInterval(async () => {
            try {
                const res = await fetch(`/api/report-status/${pageToken}`);
                if (res.ok) {
                    const data = await res.json();
                    setReportData((prev) => ({ ...prev, ...data }));
                }
            } catch {
                // Silently ignore polling errors
            }
        }, 5000);

        return () => clearInterval(interval);
    }, [pageToken, shouldPoll]);

    const handleRetryPayment = () => {
        setRetrying(true);

        router.post(
            localePath(`/report/${pageToken}/checkout`),
            {},
            {
                preserveScroll: true,
                onFinish: () => setRetrying(false),
            },
        );
    };

    const progressSteps = [
        { icon: MapPin, labelKey: "progress_analyzing", done: true },
        {
            icon: CreditCard,
            labelKey: "progress_payment",
            done: paymentConfirmedStatuses.includes(reportData.status),
        },
        {
            icon: ChartBar,
            labelKey: "progress_comparing",
            done: generationStartedStatuses.includes(reportData.status),
        },
        { icon: FilePdf, labelKey: "progress_generating", done: isCompleted },
    ];

    const sidebarNoteKey = canRetryPayment
        ? "payment_retry_note"
        : shouldPoll
          ? "auto_update_note"
          : null;

    const sidebar = !isCompleted ? (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <h3 className="mb-5 text-[0.85rem] font-semibold leading-[1.35] tracking-[-0.02em] text-brand-primary">
                    {t("sidebar_progress_title")}
                </h3>
                <div className="space-y-4">
                    {progressSteps.map(
                        ({ icon: StepIcon, labelKey, done }, i) => (
                            <div key={i} className="flex items-center gap-3">
                                <div
                                    className={`flex h-8 w-8 shrink-0 items-center justify-center ${
                                        done
                                            ? "bg-brand-primary"
                                            : "bg-slate-100"
                                    }`}
                                >
                                    <StepIcon
                                        size={16}
                                        weight={done ? "fill" : "regular"}
                                    />
                                </div>
                                <p
                                    className={`text-[14px] leading-[1.4] font-semibold ${
                                        done
                                            ? "text-brand-primary"
                                            : "text-brand-primary/55"
                                    }`}
                                >
                                    {t(labelKey)}
                                </p>
                            </div>
                        ),
                    )}
                </div>
            </div>

            {sidebarNoteKey && (
                <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                    <p className="text-sm leading-[1.7] text-brand-primary">
                        {t(sidebarNoteKey)}
                    </p>
                </div>
            )}
        </>
    ) : undefined;

    return (
        <PublicLayout>
            <Head title={t("report_status")} />
            <WizardLayout
                currentStep={4}
                sidebar={sidebar}
                reportType={reportData.report_type}
            >
                <div className="text-center lg:text-left">
                    {flash.success && (
                        <div className="mb-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {flash.success}
                        </div>
                    )}

                    {flash.error && (
                        <div className="mb-6 border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            {flash.error}
                        </div>
                    )}

                    <h2 className="mb-2 text-[2.1rem] font-bold leading-[0.98] tracking-[-0.04em] text-brand-primary md:text-[2.7rem]">
                        {t("report_status")}
                    </h2>
                    <p className="mb-8 break-all text-[14px] leading-[1.68] text-brand-primary/78">
                        {reportData.url}
                    </p>

                    {/* Animation area */}
                    <div className="py-4 mb-6">
                        {currentStatus.animation === "complete" && (
                            <CompletedAnimation />
                        )}
                        {currentStatus.animation === "processing" && (
                            <GeneratingAnimation />
                        )}
                        {currentStatus.animation === "payment" && (
                            <PaymentAnimation />
                        )}
                        {currentStatus.animation === "alert" && (
                            <AlertAnimation />
                        )}
                    </div>

                    {/* Status text */}
                    <p
                        className={`text-base font-semibold text-center ${currentStatus.colorClass}`}
                    >
                        {t(currentStatus.messageKey)}
                    </p>

                    {reportData.error_message && (
                        <p className="mt-3 text-sm text-brand-primary/78">
                            {reportData.error_message}
                        </p>
                    )}

                    {canRetryPayment && (
                        <div className="mt-6 flex justify-center lg:justify-start">
                            <button
                                type="button"
                                onClick={handleRetryPayment}
                                disabled={retrying}
                                className="inline-flex cursor-pointer items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <ArrowClockwise size={16} />
                                {retrying
                                    ? t("payment_redirecting")
                                    : t("payment_retry")}
                            </button>
                        </div>
                    )}
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
