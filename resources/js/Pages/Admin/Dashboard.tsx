import { Head, Link, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button, buttonVariants } from "@/Components/ui/button";
import { Report, ReportStatus, ReportType, PaginatedData } from "@/types";
import { cn } from "@/lib/utils";
import { CaretDown, Eye, FilePdf, PaperPlaneTilt } from "@phosphor-icons/react";

const sectionClass =
    "rounded-none border border-brand-primary/10 bg-white shadow-none";
const sectionHeaderClass =
    "rounded-none border-b border-brand-primary/10 px-5 py-4";
const sectionBodyClass = "space-y-5 px-5 py-5";
const selectClass =
    "h-11 w-full appearance-none rounded-none border border-brand-primary/15 bg-white px-3 pr-10 text-sm text-brand-primary outline-none transition-colors focus:border-brand-secondary";
const primaryButtonClass =
    "h-9 rounded-none bg-brand-primary px-3 text-white hover:bg-brand-primary/92";
const outlineButtonClass =
    "rounded-none border-brand-primary/15 text-brand-primary hover:bg-brand-primary/4";

const statusConfig: Record<ReportStatus, { label: string; tone: string }> = {
    not_accessible: {
        label: "Inaccesibil",
        tone: "text-brand-secondary",
    },
    pending: {
        label: "În așteptare",
        tone: "text-brand-tertiary",
    },
    awaiting_payment: {
        label: "Așteaptă plata",
        tone: "text-amber-700",
    },
    payment_processing: {
        label: "Plată în confirmare",
        tone: "text-sky-700",
    },
    payment_cancelled: {
        label: "Plată anulată",
        tone: "text-amber-700",
    },
    payment_failed: {
        label: "Plată eșuată",
        tone: "text-red-600",
    },
    test_completed: {
        label: "Test finalizat",
        tone: "text-emerald-700",
    },
    to_be_sent: {
        label: "De trimis",
        tone: "text-brand-secondary",
    },
    sent: {
        label: "Trimis",
        tone: "text-emerald-700",
    },
    error: {
        label: "Eroare",
        tone: "text-red-600",
    },
};

const typeLabels: Record<ReportType, string> = {
    rental_living: "Închiriere – Rezidențial",
    rental_business: "Închiriere – Business",
    buying_living: "Cumpărare – Rezidențial",
    buying_business: "Cumpărare – Business",
};

interface DashboardCounts {
    total: number;
    pending: number;
    awaiting_payment: number;
    payment_processing: number;
    payment_cancelled: number;
    payment_failed: number;
    test_completed?: number;
    to_be_sent: number;
    sent: number;
    error: number;
    not_accessible: number;
}

interface DashboardFilters {
    status?: string;
    report_type?: string;
    [key: string]: string | undefined;
}

interface DashboardProps {
    reports: PaginatedData<Report>;
    counts: DashboardCounts;
    filters: DashboardFilters;
}

function canEmailReport(report: Report): boolean {
    return (
        !report.is_test &&
        Boolean(report.email) &&
        (report.status === "to_be_sent" ||
            report.status === "sent" ||
            Boolean(report.report_url))
    );
}

function canSendFeedbackEmail(report: Report): boolean {
    return (
        !report.is_test &&
        Boolean(report.email) &&
        Boolean(report.processed_at) &&
        (report.status === "to_be_sent" ||
            report.status === "sent" ||
            Boolean(report.report_url))
    );
}

function emailActionLabel(report: Report): string {
    return report.status === "sent" ? "Retrimite" : "Trimite";
}

function feedbackActionLabel(report: Report): string {
    return report.feedback_sent_at ? "Retrimite feedback" : "Trimite feedback";
}

function truncate(str: string | null, len = 88): string {
    if (!str) {
        return "";
    }

    return str.length > len ? `${str.substring(0, len)}…` : str;
}

function formatDateTime(value: string | null): string {
    if (!value) {
        return "-";
    }

    return new Date(value).toLocaleString("ro-RO", {
        day: "2-digit",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function affiliateLabel(report: Report): string {
    return report.affiliate_ref || "-";
}

function ActionableReportCard({ report }: { report: Report }) {
    return (
        <Card className={sectionClass}>
            <CardContent className="space-y-4 px-5 py-5">
                <div className="space-y-1">
                    <p className="text-sm font-medium text-brand-primary">
                        {typeLabels[report.report_type] ?? report.report_type}
                    </p>
                    <p
                        className={cn(
                            "text-sm",
                            statusConfig[report.status].tone,
                        )}
                    >
                        {statusConfig[report.status].label}
                    </p>
                    {report.is_test && (
                        <p className="text-sm text-brand-primary/60">
                            Flux de test facturare
                        </p>
                    )}
                </div>

                <div className="space-y-2 text-sm text-brand-primary/68">
                    <p>Raport #{report.id}</p>
                    <p>Afiliat: {affiliateLabel(report)}</p>
                    <p>{report.email || "Fără email"}</p>
                    <p>{formatDateTime(report.created_at)}</p>
                </div>

                <p className="break-all text-sm leading-6 text-brand-primary/78">
                    {truncate(report.url, 120)}
                </p>

                <div className="flex flex-wrap gap-2">
                    <Button
                        size="sm"
                        className={primaryButtonClass}
                        onClick={() =>
                            router.post(`/admin/reports/${report.id}/send`)
                        }
                    >
                        <PaperPlaneTilt size={15} weight="duotone" />
                        {emailActionLabel(report)}
                    </Button>

                    {canSendFeedbackEmail(report) && (
                        <Button
                            size="sm"
                            variant="outline"
                            className={outlineButtonClass}
                            onClick={() =>
                                router.post(
                                    `/admin/reports/${report.id}/feedback/send`,
                                )
                            }
                        >
                            {feedbackActionLabel(report)}
                        </Button>
                    )}

                    <Link
                        href={`/admin/reports/${report.id}`}
                        className={cn(
                            buttonVariants({ variant: "outline", size: "sm" }),
                            outlineButtonClass,
                            "h-9 px-3",
                        )}
                    >
                        <Eye size={15} weight="duotone" />
                        Detalii
                    </Link>
                </div>
            </CardContent>
        </Card>
    );
}

function MobileReportRow({ report }: { report: Report }) {
    return (
        <div className="space-y-4 border-b border-brand-primary/10 px-5 py-4 last:border-b-0">
            <div className="space-y-1">
                <p className="text-sm font-medium text-brand-primary">
                    Raport #{report.id}
                </p>
                <h3 className="text-base font-semibold leading-6 text-brand-primary">
                    {typeLabels[report.report_type] ?? report.report_type}
                </h3>
                <p className={cn("text-sm", statusConfig[report.status].tone)}>
                    {statusConfig[report.status].label}
                </p>
                {report.is_test && (
                    <p className="text-sm text-brand-primary/60">
                        Flux de test facturare
                    </p>
                )}
            </div>

            <p className="text-sm text-brand-primary/60">
                {formatDateTime(report.created_at)}
                {report.processed_at
                    ? ` · Procesat ${formatDateTime(report.processed_at)}`
                    : ""}
            </p>

            <p className="break-all text-sm leading-6 text-brand-primary/78">
                {truncate(report.url, 140)}
            </p>

            <p className="text-sm text-brand-primary/68">
                {report.email || "Fără email"}
            </p>

            <p className="text-sm text-brand-primary/68">
                Afiliat: {affiliateLabel(report)}
            </p>

            <div className="flex flex-wrap gap-2">
                {canEmailReport(report) && (
                    <Button
                        size="sm"
                        className={primaryButtonClass}
                        onClick={() =>
                            router.post(`/admin/reports/${report.id}/send`)
                        }
                    >
                        <PaperPlaneTilt size={15} weight="duotone" />
                        {emailActionLabel(report)}
                    </Button>
                )}

                {report.report_url && (
                    <a
                        href={`/admin/reports/${report.id}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={cn(
                            buttonVariants({ variant: "outline", size: "sm" }),
                            outlineButtonClass,
                            "h-9 px-3",
                        )}
                    >
                        <FilePdf size={15} weight="duotone" />
                        PDF
                    </a>
                )}

                {canSendFeedbackEmail(report) && (
                    <Button
                        size="sm"
                        variant="outline"
                        className={outlineButtonClass}
                        onClick={() =>
                            router.post(
                                `/admin/reports/${report.id}/feedback/send`,
                            )
                        }
                    >
                        {feedbackActionLabel(report)}
                    </Button>
                )}

                <Link
                    href={`/admin/reports/${report.id}`}
                    className={cn(
                        buttonVariants({ variant: "outline", size: "sm" }),
                        outlineButtonClass,
                        "h-9 px-3",
                    )}
                >
                    <Eye size={15} weight="duotone" />
                    Detalii
                </Link>
            </div>
        </div>
    );
}

export default function Dashboard({
    reports,
    counts,
    filters,
}: DashboardProps) {
    const actionableReports = reports.data.filter(
        (report) => report.status === "to_be_sent",
    );
    const summaryCards = [
        {
            label: "Total rapoarte",
            value: counts.total,
            tone: "text-brand-primary",
        },
        {
            label: "De trimis",
            value: counts.to_be_sent,
            tone: "text-brand-secondary",
        },
        { label: "Trimise", value: counts.sent, tone: "text-emerald-700" },
        { label: "Eroare", value: counts.error, tone: "text-red-600" },
    ];
    const statusOptions = [
        { value: "", label: "Toate statusurile" },
        ...Object.entries(statusConfig).map(([value, config]) => ({
            value,
            label: config.label,
        })),
    ];

    const applyStatusFilter = (value: string) => {
        const params: Record<string, string> = {};

        if (filters.report_type) {
            params.report_type = filters.report_type;
        }

        if (value) {
            params.status = value;
        }

        router.get("/admin/dashboard", params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AdminLayout>
            <Head title="Panou de control" />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 border-b border-brand-primary/10 pb-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-brand-primary">
                            Panou de control
                        </h1>
                        <p className="mt-1 text-sm text-brand-primary/60">
                            Rapoarte de trimis, filtrare rapidă și acțiuni
                            dintr-un singur loc.
                        </p>
                    </div>

                    <p className="text-sm text-brand-primary/60">
                        În total: {counts.total} rapoarte
                    </p>
                </div>

                <div className="hidden gap-4 sm:grid sm:grid-cols-2 xl:grid-cols-4">
                    {summaryCards.map((card) => (
                        <Card key={card.label} className={sectionClass}>
                            <CardContent className="space-y-2 px-5 py-5">
                                <p className="text-sm text-brand-primary/60">
                                    {card.label}
                                </p>
                                <p
                                    className={cn(
                                        "text-3xl font-semibold",
                                        card.tone,
                                    )}
                                >
                                    {card.value}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <CardTitle className="text-base font-semibold text-brand-primary">
                                Rapoarte de trimis
                            </CardTitle>
                            <p className="text-sm text-brand-primary/60">
                                În această pagină: {actionableReports.length}
                            </p>
                        </div>
                    </CardHeader>
                    <CardContent className={sectionBodyClass}>
                        {actionableReports.length === 0 ? (
                            <p className="text-sm text-brand-primary/60">
                                Nu există rapoarte gata de trimis în pagina
                                curentă.
                            </p>
                        ) : (
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                {actionableReports.map((report) => (
                                    <ActionableReportCard
                                        key={report.id}
                                        report={report}
                                    />
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <div className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <CardTitle className="text-base font-semibold text-brand-primary">
                                    Toate rapoartele
                                </CardTitle>
                                <p className="mt-1 text-sm text-brand-primary/60">
                                    Listă completă filtrabilă după status.
                                </p>
                            </div>

                            <label className="block w-full max-w-xs">
                                <span className="mb-2 block text-sm font-medium text-brand-primary">
                                    Status
                                </span>
                                <div className="relative">
                                    <select
                                        value={filters.status || ""}
                                        onChange={(event) =>
                                            applyStatusFilter(
                                                event.target.value,
                                            )
                                        }
                                        className={selectClass}
                                    >
                                        {statusOptions.map(
                                            ({ value, label }) => (
                                                <option
                                                    key={value || "all"}
                                                    value={value}
                                                >
                                                    {label}
                                                </option>
                                            ),
                                        )}
                                    </select>
                                    <CaretDown
                                        size={16}
                                        className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-brand-primary/48"
                                    />
                                </div>
                            </label>
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <div className="lg:hidden">
                            {reports.data.length === 0 ? (
                                <div className="px-5 py-10 text-center text-sm text-brand-primary/60">
                                    Nu s-au găsit rapoarte.
                                </div>
                            ) : (
                                reports.data.map((report) => (
                                    <MobileReportRow
                                        key={report.id}
                                        report={report}
                                    />
                                ))
                            )}
                        </div>

                        <div className="hidden overflow-x-auto lg:block">
                            <table className="min-w-[1100px] w-full text-sm">
                                <thead>
                                    <tr className="border-b border-brand-primary/10 bg-brand-primary/2">
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Raport
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Proprietate
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Email
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Afiliat
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Status
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Creat
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Acțiuni
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {reports.data.length === 0 && (
                                        <tr>
                                            <td
                                                colSpan={7}
                                                className="px-5 py-10 text-center text-sm text-brand-primary/60"
                                            >
                                                Nu s-au găsit rapoarte.
                                            </td>
                                        </tr>
                                    )}

                                    {reports.data.map((report) => (
                                        <tr
                                            key={report.id}
                                            className="border-b border-brand-primary/10 align-top transition-colors hover:bg-brand-primary/3"
                                        >
                                            <td className="px-5 py-4">
                                                <p className="text-sm font-medium text-brand-primary">
                                                    Raport #{report.id}
                                                </p>
                                                <p className="mt-1 text-sm text-brand-primary/72">
                                                    {typeLabels[
                                                        report.report_type
                                                    ] ?? report.report_type}
                                                </p>
                                                {report.is_test && (
                                                    <p className="mt-1 text-sm text-brand-primary/60">
                                                        Flux de test facturare
                                                    </p>
                                                )}
                                            </td>
                                            <td className="px-5 py-4">
                                                <p
                                                    className="max-w-[24rem] text-sm leading-6 text-brand-primary/76"
                                                    title={report.url}
                                                >
                                                    {truncate(report.url, 96)}
                                                </p>
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/70">
                                                {report.email || "—"}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/70">
                                                {affiliateLabel(report)}
                                            </td>
                                            <td className="px-5 py-4">
                                                <p
                                                    className={cn(
                                                        "text-sm font-medium",
                                                        statusConfig[
                                                            report.status
                                                        ].tone,
                                                    )}
                                                >
                                                    {
                                                        statusConfig[
                                                            report.status
                                                        ].label
                                                    }
                                                </p>
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/68">
                                                {formatDateTime(
                                                    report.created_at,
                                                )}
                                            </td>
                                            <td className="px-5 py-4">
                                                <div className="flex flex-wrap gap-2">
                                                    {canEmailReport(report) && (
                                                        <Button
                                                            size="sm"
                                                            className={
                                                                primaryButtonClass
                                                            }
                                                            onClick={() =>
                                                                router.post(
                                                                    `/admin/reports/${report.id}/send`,
                                                                )
                                                            }
                                                        >
                                                            <PaperPlaneTilt
                                                                size={15}
                                                                weight="duotone"
                                                            />
                                                            {emailActionLabel(
                                                                report,
                                                            )}
                                                        </Button>
                                                    )}

                                                    {report.report_url && (
                                                        <a
                                                            href={`/admin/reports/${report.id}/pdf`}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className={cn(
                                                                buttonVariants({
                                                                    variant:
                                                                        "outline",
                                                                    size: "sm",
                                                                }),
                                                                outlineButtonClass,
                                                                "h-9 px-3",
                                                            )}
                                                        >
                                                            <FilePdf
                                                                size={15}
                                                                weight="duotone"
                                                            />
                                                            PDF
                                                        </a>
                                                    )}

                                                    {canSendFeedbackEmail(
                                                        report,
                                                    ) && (
                                                        <Button
                                                            size="sm"
                                                            variant="outline"
                                                            className={
                                                                outlineButtonClass
                                                            }
                                                            onClick={() =>
                                                                router.post(
                                                                    `/admin/reports/${report.id}/feedback/send`,
                                                                )
                                                            }
                                                        >
                                                            {feedbackActionLabel(
                                                                report,
                                                            )}
                                                        </Button>
                                                    )}

                                                    <Link
                                                        href={`/admin/reports/${report.id}`}
                                                        className={cn(
                                                            buttonVariants({
                                                                variant:
                                                                    "outline",
                                                                size: "sm",
                                                            }),
                                                            outlineButtonClass,
                                                            "h-9 px-3",
                                                        )}
                                                    >
                                                        <Eye
                                                            size={15}
                                                            weight="duotone"
                                                        />
                                                        Detalii
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {reports.links && reports.links.length > 3 && (
                            <div className="flex flex-wrap items-center justify-center gap-2 border-t border-brand-primary/10 px-5 py-4">
                                {reports.links.map((link, index) => (
                                    <button
                                        key={index}
                                        disabled={!link.url}
                                        onClick={() =>
                                            link.url &&
                                            router.get(
                                                link.url,
                                                {},
                                                { preserveState: true },
                                            )
                                        }
                                        className={cn(
                                            "border border-brand-primary/15 px-3 py-1.5 text-sm transition-colors",
                                            link.active
                                                ? "bg-brand-primary text-white"
                                                : "text-brand-primary/70 hover:bg-brand-primary/4",
                                            !link.url &&
                                                "cursor-not-allowed opacity-50",
                                        )}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
