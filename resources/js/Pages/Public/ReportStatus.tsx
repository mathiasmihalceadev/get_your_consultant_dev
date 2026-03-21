import { useState, useEffect } from "react";
import { Head } from "@inertiajs/react";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import {
    CircleNotch,
    Clock,
    CheckCircle,
    XCircle,
    Warning,
    Icon,
    IconWeight,
} from "@phosphor-icons/react";
import { Report, ReportType, ReportStatus as ReportStatusType } from "@/types";

const typeLabels: Record<ReportType, string> = {
    purchase: "Purchase Report",
    rental: "Rental Report",
    commercial: "Commercial Report",
};

const typeBadgeColors: Record<ReportType, string> = {
    purchase: "bg-violet-100 text-violet-800",
    rental: "bg-teal-100 text-teal-800",
    commercial: "bg-amber-100 text-amber-800",
};

interface StatusConfigItem {
    icon: Icon;
    iconProps: { className: string; weight?: IconWeight };
    label: string;
    color: string;
}

const statusConfig: Record<ReportStatusType, StatusConfigItem> = {
    pending: {
        icon: CircleNotch,
        iconProps: { className: "text-blue-500 animate-spin", weight: "bold" },
        label: "Your report is being generated…",
        color: "text-blue-600",
    },
    to_be_sent: {
        icon: Clock,
        iconProps: { className: "text-orange-500", weight: "fill" },
        label: "Your report is ready and will be sent shortly.",
        color: "text-orange-600",
    },
    sent: {
        icon: CheckCircle,
        iconProps: { className: "text-green-500", weight: "fill" },
        label: "Your report has been sent to your email.",
        color: "text-green-600",
    },
    error: {
        icon: XCircle,
        iconProps: { className: "text-red-500", weight: "fill" },
        label: "There was an error generating your report. Our team has been notified.",
        color: "text-red-600",
    },
    not_accessible: {
        icon: Warning,
        iconProps: { className: "text-amber-500", weight: "fill" },
        label: "This URL could not be accessed.",
        color: "text-amber-600",
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
    const [reportData, setReportData] = useState<Report>(initialReport);

    useEffect(() => {
        if (["sent", "error", "not_accessible"].includes(reportData.status)) {
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

    const config = statusConfig[reportData.status] || statusConfig.pending;
    const StatusIcon = config.icon;

    return (
        <>
            <Head title="Report Status" />
            <div className="min-h-screen bg-white flex items-center justify-center px-4 py-16">
                <Card className="max-w-lg w-full">
                    <CardHeader className="text-center">
                        <div className="flex justify-center gap-2 mb-3">
                            <span
                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeBadgeColors[reportData.report_type] || ""}`}
                            >
                                {typeLabels[reportData.report_type]}
                            </span>
                        </div>
                        <CardTitle className="text-xl">Report Status</CardTitle>
                    </CardHeader>
                    <CardContent className="text-center space-y-6">
                        <p className="text-sm text-muted-foreground break-all">
                            {reportData.url}
                        </p>

                        <div className="flex flex-col items-center gap-3 py-6 transition-all duration-300">
                            <StatusIcon size={56} {...config.iconProps} />
                            <p
                                className={`text-sm font-medium ${config.color}`}
                            >
                                {config.label}
                            </p>
                        </div>

                        {reportData.status === "pending" && (
                            <p className="text-xs text-muted-foreground">
                                This page updates automatically every few
                                seconds.
                            </p>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
