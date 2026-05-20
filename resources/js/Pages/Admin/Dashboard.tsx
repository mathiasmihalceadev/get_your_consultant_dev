import { Head, Link, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import { Button } from "@/Components/ui/button";
import { Report, ReportType, ReportStatus, PaginatedData } from "@/types";
import {
    CreditCard,
    PaperPlaneTilt,
    FilePdf,
    Eye,
    ClockCountdown,
    EnvelopeSimple,
    Warning,
    CheckCircle,
    XCircle,
    ListDashes,
} from "@phosphor-icons/react";

const statusConfig: Record<
    ReportStatus,
    { label: string; bg: string; text: string }
> = {
    not_accessible: {
        label: "Inaccesibil",
        bg: "bg-brand-secondary/10",
        text: "text-brand-secondary",
    },
    pending: {
        label: "În așteptare",
        bg: "bg-brand-tertiary/10",
        text: "text-brand-tertiary",
    },
    awaiting_payment: {
        label: "Așteaptă plata",
        bg: "bg-amber-50",
        text: "text-amber-700",
    },
    payment_processing: {
        label: "Plată în confirmare",
        bg: "bg-sky-50",
        text: "text-sky-700",
    },
    payment_cancelled: {
        label: "Plată anulată",
        bg: "bg-amber-50",
        text: "text-amber-700",
    },
    payment_failed: {
        label: "Plată eșuată",
        bg: "bg-red-50",
        text: "text-red-600",
    },
    to_be_sent: {
        label: "De trimis",
        bg: "bg-brand-secondary/15",
        text: "text-brand-secondary",
    },
    sent: {
        label: "Trimis",
        bg: "bg-emerald-50",
        text: "text-emerald-700",
    },
    error: {
        label: "Eroare",
        bg: "bg-red-50",
        text: "text-red-600",
    },
};

const typeConfig: Record<
    ReportType,
    { label: string; bg: string; text: string }
> = {
    rental_living: {
        label: "Închiriere – Rezidențial",
        bg: "bg-brand-tertiary/10",
        text: "text-brand-tertiary",
    },
    rental_business: {
        label: "Închiriere – Business",
        bg: "bg-brand-secondary/10",
        text: "text-brand-secondary",
    },
    buying_living: {
        label: "Cumpărare – Rezidențial",
        bg: "bg-brand-primary/10",
        text: "text-brand-primary",
    },
    buying_business: {
        label: "Cumpărare – Business",
        bg: "bg-brand-neutral/10",
        text: "text-brand-neutral",
    },
};

function truncate(str: string | null, len = 40): string {
    if (!str) return "";
    return str.length > len ? str.substring(0, len) + "…" : str;
}

interface DashboardCounts {
    total: number;
    pending: number;
    awaiting_payment: number;
    payment_processing: number;
    payment_cancelled: number;
    payment_failed: number;
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

export default function Dashboard({
    reports,
    counts,
    filters,
}: DashboardProps) {
    const applyFilter = (key: string, value: string) => {
        const params: Record<string, string> = {
            ...(filters as Record<string, string>),
            [key]: value,
        };
        if (!value) delete params[key];
        router.get("/admin/dashboard", params, { preserveState: true });
    };

    return (
        <AdminLayout>
            <Head title="Panou de Control" />

            <h1 className="text-2xl font-bold text-brand-primary mb-6">
                Panou de Control
            </h1>

            {/* Summary cards */}
            <div className="grid grid-cols-2 gap-4 mb-6 md:grid-cols-4 xl:grid-cols-5">
                {[
                    {
                        label: "Total",
                        count: counts.total,
                        color: "text-brand-primary",
                        icon: ListDashes,
                    },
                    {
                        label: "În așteptare",
                        count: counts.pending,
                        color: "text-brand-tertiary",
                        icon: ClockCountdown,
                    },
                    {
                        label: "Așteaptă plata",
                        count: counts.awaiting_payment,
                        color: "text-amber-600",
                        icon: CreditCard,
                    },
                    {
                        label: "Plată în confirmare",
                        count: counts.payment_processing,
                        color: "text-sky-600",
                        icon: ClockCountdown,
                    },
                    {
                        label: "Plată anulată",
                        count: counts.payment_cancelled,
                        color: "text-amber-600",
                        icon: XCircle,
                    },
                    {
                        label: "Plată eșuată",
                        count: counts.payment_failed,
                        color: "text-red-500",
                        icon: Warning,
                    },
                    {
                        label: "De trimis",
                        count: counts.to_be_sent,
                        color: "text-brand-secondary",
                        icon: EnvelopeSimple,
                    },
                    {
                        label: "Trimis",
                        count: counts.sent,
                        color: "text-emerald-600",
                        icon: CheckCircle,
                    },
                    {
                        label: "Eroare",
                        count: counts.error,
                        color: "text-red-500",
                        icon: XCircle,
                    },
                    {
                        label: "Inaccesibil",
                        count: counts.not_accessible,
                        color: "text-brand-secondary",
                        icon: Warning,
                    },
                ].map(({ label, count, color, icon: Icon }) => (
                    <Card key={label}>
                        <CardContent className="pt-4 pb-4 text-center">
                            <Icon
                                size={20}
                                weight="duotone"
                                className={`mx-auto mb-1 ${color}`}
                            />
                            <div className={`text-2xl font-bold ${color}`}>
                                {count}
                            </div>
                            <div className="text-xs text-brand-neutral mt-1">
                                {label}
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Filters */}
            <div className="flex flex-wrap items-center gap-3 mb-4">
                <div className="flex flex-wrap gap-1.5">
                    {[
                        { value: "all", label: "Toate" },
                        ...Object.entries(statusConfig).map(
                            ([key, { label }]) => ({
                                value: key,
                                label,
                            }),
                        ),
                    ].map(({ value, label }) => (
                        <button
                            key={value}
                            type="button"
                            onClick={() =>
                                applyFilter(
                                    "status",
                                    value === "all" ? "" : value,
                                )
                            }
                            className={`px-3 py-1.5 text-xs font-medium transition-colors cursor-pointer ${
                                (filters.status || "all") === value
                                    ? "bg-brand-primary text-white"
                                    : "bg-muted text-brand-neutral hover:bg-muted/80"
                            }`}
                        >
                            {label}
                        </button>
                    ))}
                </div>

                <select
                    value={filters.report_type || ""}
                    onChange={(e) => applyFilter("report_type", e.target.value)}
                    className="text-xs font-medium border border-border px-3 py-1.5 bg-white text-brand-primary cursor-pointer"
                >
                    <option value="">Toate Tipurile</option>
                    {Object.entries(typeConfig).map(([key, { label }]) => (
                        <option key={key} value={key}>
                            {label}
                        </option>
                    ))}
                </select>
            </div>

            {/* Reports table */}
            <Card>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-brand-primary/3">
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        ID
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        Tip
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        URL
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        Email
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        Stare
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        Creat
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        Procesat
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-brand-primary uppercase tracking-wide">
                                        Acțiuni
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {reports.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={8}
                                            className="px-4 py-8 text-center text-brand-neutral"
                                        >
                                            Nu s-au găsit rapoarte.
                                        </td>
                                    </tr>
                                )}
                                {reports.data.map((report) => (
                                    <tr
                                        key={report.id}
                                        className="border-b border-border/50 hover:bg-brand-primary/2 transition-colors"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs text-brand-primary font-semibold">
                                            {report.id}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 text-xs font-medium ${typeConfig[report.report_type]?.bg ?? ""} ${typeConfig[report.report_type]?.text ?? ""}`}
                                            >
                                                {typeConfig[report.report_type]
                                                    ?.label ??
                                                    report.report_type}
                                            </span>
                                        </td>
                                        <td
                                            className="px-4 py-3"
                                            title={report.url}
                                        >
                                            <span className="text-xs text-brand-primary/70">
                                                {truncate(report.url)}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-primary/70">
                                            {report.email || "—"}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 text-xs font-medium ${statusConfig[report.status]?.bg ?? ""} ${statusConfig[report.status]?.text ?? ""}`}
                                            >
                                                {statusConfig[report.status]
                                                    ?.label ?? report.status}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-neutral">
                                            {new Date(
                                                report.created_at,
                                            ).toLocaleDateString()}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-neutral">
                                            {report.processed_at
                                                ? new Date(
                                                      report.processed_at,
                                                  ).toLocaleDateString()
                                                : "—"}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-1.5">
                                                {report.status ===
                                                    "to_be_sent" && (
                                                    <button
                                                        type="button"
                                                        title="Trimite"
                                                        className="p-1.5 text-brand-secondary hover:bg-brand-secondary/10 transition-colors cursor-pointer"
                                                        onClick={() =>
                                                            router.post(
                                                                `/admin/reports/${report.id}/send`,
                                                            )
                                                        }
                                                    >
                                                        <PaperPlaneTilt
                                                            size={16}
                                                            weight="duotone"
                                                        />
                                                    </button>
                                                )}
                                                {report.report_url && (
                                                    <a
                                                        href={report.report_url}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        title="PDF"
                                                        className="p-1.5 text-brand-tertiary hover:bg-brand-tertiary/10 transition-colors"
                                                    >
                                                        <FilePdf
                                                            size={16}
                                                            weight="duotone"
                                                        />
                                                    </a>
                                                )}
                                                <Link
                                                    href={`/admin/reports/${report.id}`}
                                                    title="Detalii"
                                                    className="p-1.5 text-brand-primary hover:bg-brand-primary/10 transition-colors"
                                                >
                                                    <Eye
                                                        size={16}
                                                        weight="duotone"
                                                    />
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {reports.links && reports.links.length > 3 && (
                        <div className="flex items-center justify-center gap-1 p-4 border-t">
                            {reports.links.map((link, i) => (
                                <button
                                    key={i}
                                    disabled={!link.url}
                                    onClick={() =>
                                        link.url &&
                                        router.get(
                                            link.url,
                                            {},
                                            { preserveState: true },
                                        )
                                    }
                                    className={`px-3 py-1 text-sm rounded cursor-pointer ${
                                        link.active
                                            ? "bg-brand-primary text-white"
                                            : "text-gray-600 hover:bg-gray-100"
                                    } ${!link.url ? "opacity-50 cursor-not-allowed" : ""}`}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ))}
                        </div>
                    )}
                </CardContent>
            </Card>
        </AdminLayout>
    );
}
