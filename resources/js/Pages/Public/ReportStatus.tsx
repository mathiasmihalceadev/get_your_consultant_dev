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
                                ? "#303048"
                                : i % 3 === 1
                                  ? "#f5915d"
                                  : "#0073f0",
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
        rental_living: t("type_rental_living"),
        rental_business: t("type_rental_business"),
        buying_living: t("type_buying_living"),
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
        <div className="space-y-6">
            <div className="bg-gray-50 border border-gray-200 p-6">
                <h3 className="text-xs font-bold text-brand-primary uppercase tracking-widest mb-5">
                    {t("sidebar_progress_title")}
                </h3>
                <div className="space-y-4">
                    {progressSteps.map(
                        ({ icon: StepIcon, labelKey, done }, i) => (
                            <div key={i} className="flex items-center gap-3">
                                <div
                                    className={`w-8 h-8 flex items-center justify-center flex-shrink-0 ${
                                        done
                                            ? "bg-brand-secondary"
                                            : "bg-gray-200"
                                    }`}
                                >
                                    <StepIcon
                                        size={16}
                                        weight={done ? "fill" : "regular"}
                                        className={
                                            done
                                                ? "text-white"
                                                : "text-gray-500"
                                        }
                                    />
                                </div>
                                <p
                                    className={`text-sm font-medium ${
                                        done
                                            ? "text-brand-primary"
                                            : "text-gray-400"
                                    }`}
                                >
                                    {t(labelKey)}
                                </p>
                            </div>
                        ),
                    )}
                </div>
            </div>

            <div className="border border-gray-200 p-6">
                <p className="text-xs text-brand-neutral leading-relaxed">
                    {t("auto_update_note")}
                </p>
            </div>
        </div>
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
                    <h2 className="text-2xl md:text-3xl font-bold text-brand-primary mb-2 tracking-tight">
                        {t("report_status")}
                    </h2>
                    <p className="text-sm text-brand-neutral mb-8 break-all">
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
