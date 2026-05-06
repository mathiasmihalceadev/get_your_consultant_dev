import { useState, useEffect } from "react";
import { Head } from "@inertiajs/react";
import { CheckCircle, FilePdf, ChartBar, MapPin } from "@phosphor-icons/react";
import { Report, ReportType, ReportStatus as ReportStatusType } from "@/types";
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
                                  ? "#F68C56"
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

interface ReportStatusProps {
    report: Report;
    pageToken: string;
}

export default function ReportStatus({
    report: initialReport,
    pageToken,
}: ReportStatusProps) {
    const { t } = useTranslation();
    const [reportData, setReportData] = useState<Report>(initialReport);

    const typeLabels: Record<ReportType, string> = {
        rental_living: t("rental"),
        rental_business: t("type_rental_business"),
        buying_living: t("buying"),
        buying_business: t("type_buying_business"),
    };

    useEffect(() => {
        if (
            reportData.status === "sent" ||
            reportData.status === "to_be_sent"
        ) {
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
    }, [reportData.status, pageToken]);

    const isCompleted =
        reportData.status === "sent" || reportData.status === "to_be_sent";

    const progressSteps = [
        { icon: MapPin, labelKey: "progress_analyzing", done: true },
        {
            icon: ChartBar,
            labelKey: "progress_comparing",
            done: reportData.status !== "pending",
        },
        { icon: FilePdf, labelKey: "progress_generating", done: isCompleted },
    ];

    const sidebar = !isCompleted ? (
        <>
            <div className="border solid-border solid-border-warm bg-white p-6">
                <h3 className="mb-5 text-xs font-bold uppercase tracking-widest text-brand-secondary">
                    {t("sidebar_progress_title")}
                </h3>
                <div className="space-y-4">
                    {progressSteps.map(
                        ({ icon: StepIcon, labelKey, done }, i) => (
                            <div key={i} className="flex items-center gap-3">
                                <div
                                    className={`flex h-8 w-8 flex-shrink-0 items-center justify-center ${
                                        done
                                            ? "bg-brand-primary"
                                            : "bg-slate-100"
                                    }`}
                                >
                                    <StepIcon
                                        size={16}
                                        weight={done ? "fill" : "regular"}
                                        className={
                                            done
                                                ? "text-white"
                                                : "text-brand-primary/55"
                                        }
                                    />
                                </div>
                                <p
                                    className={`text-sm font-semibold ${
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

            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)] p-6">
                <p className="text-sm leading-[1.7] text-brand-primary">
                    {t("auto_update_note")}
                </p>
            </div>
        </>
    ) : undefined;

    return (
        <PublicLayout>
            <Head title={t("report_status")} />
            <WizardLayout currentStep={4} sidebar={sidebar}>
                <div className="text-center lg:text-left">
                    <div className="mb-1">
                        <span className="inline-flex items-center px-2.5 py-0.5 bg-brand-primary/10 text-brand-primary text-xs font-semibold">
                            {typeLabels[reportData.report_type]}
                        </span>
                    </div>
                    <p className="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-brand-secondary">
                        {t("wizard_step_status")}
                    </p>
                    <h2 className="mb-2 text-[2rem] font-bold tracking-[-0.035em] text-brand-primary md:text-[2.45rem]">
                        {t("report_status")}
                    </h2>
                    <p className="mb-8 break-all text-sm leading-[1.7] text-brand-primary">
                        {reportData.url}
                    </p>

                    {/* Animation area */}
                    <div className="py-4 mb-6">
                        {!isCompleted && <GeneratingAnimation />}
                        {isCompleted && <CompletedAnimation />}
                    </div>

                    {/* Status text */}
                    <p
                        className={`text-base font-semibold ${isCompleted ? "text-green-600" : "text-brand-tertiary"} text-center`}
                    >
                        {isCompleted
                            ? t(
                                  reportData.status === "sent"
                                      ? "status_sent"
                                      : "status_to_be_sent",
                              )
                            : t("status_pending")}
                    </p>
                </div>
            </WizardLayout>
        </PublicLayout>
    );
}
