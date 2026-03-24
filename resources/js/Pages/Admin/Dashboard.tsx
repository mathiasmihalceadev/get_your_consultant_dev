import { Head, Link, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import { Button } from "@/Components/ui/button";
import { Tabs, TabsList, TabsTrigger } from "@/Components/ui/tabs";
import { Report, ReportType, ReportStatus, PaginatedData } from "@/types";

const statusBadgeColors: Record<ReportStatus, string> = {
    not_accessible: "bg-yellow-100 text-yellow-800",
    pending: "bg-blue-100 text-blue-800",
    to_be_sent: "bg-orange-100 text-orange-800",
    sent: "bg-green-100 text-green-800",
    error: "bg-red-100 text-red-800",
};

const typeBadgeColors: Record<ReportType, string> = {
    rental_living: "bg-teal-100 text-teal-800",
    rental_business: "bg-amber-100 text-amber-800",
    buying_living: "bg-violet-100 text-violet-800",
    buying_business: "bg-rose-100 text-rose-800",
};

const statusLabels: Record<ReportStatus, string> = {
    not_accessible: "Inaccesibil",
    pending: "În așteptare",
    to_be_sent: "De trimis",
    sent: "Trimis",
    error: "Eroare",
};

function truncate(str: string | null, len = 40): string {
    if (!str) return "";
    return str.length > len ? str.substring(0, len) + "…" : str;
}

interface DashboardCounts {
    total: number;
    pending: number;
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
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                {[
                    {
                        label: "Total",
                        count: counts.total,
                        color: "text-gray-900",
                    },
                    {
                        label: "În așteptare",
                        count: counts.pending,
                        color: "text-blue-600",
                    },
                    {
                        label: "De trimis",
                        count: counts.to_be_sent,
                        color: "text-orange-600",
                    },
                    {
                        label: "Trimis",
                        count: counts.sent,
                        color: "text-green-600",
                    },
                    {
                        label: "Eroare",
                        count: counts.error,
                        color: "text-red-600",
                    },
                    {
                        label: "Inaccesibil",
                        count: counts.not_accessible,
                        color: "text-yellow-600",
                    },
                ].map(({ label, count, color }) => (
                    <Card key={label}>
                        <CardContent className="pt-4 pb-4 text-center">
                            <div className={`text-2xl font-bold ${color}`}>
                                {count}
                            </div>
                            <div className="text-xs text-muted-foreground mt-1">
                                {label}
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Filters */}
            <div className="flex flex-wrap items-center gap-4 mb-4">
                <Tabs
                    value={filters.status || "all"}
                    onValueChange={(v) =>
                        applyFilter("status", v === "all" ? "" : v)
                    }
                >
                    <TabsList>
                        <TabsTrigger value="all">All</TabsTrigger>
                        {Object.entries(statusLabels).map(([key, label]) => (
                            <TabsTrigger key={key} value={key}>
                                {label}
                            </TabsTrigger>
                        ))}
                    </TabsList>
                </Tabs>

                <select
                    value={filters.report_type || ""}
                    onChange={(e) => applyFilter("report_type", e.target.value)}
                    className="text-sm border rounded-md px-3 py-1.5 bg-white"
                >
                    <option value="">Toate Tipurile</option>
                    <option value="rental_living">
                        Închiriere – Rezidențial
                    </option>
                    <option value="rental_business">
                        Închiriere – Business
                    </option>
                    <option value="buying_living">
                        Cumpărare – Rezidențial
                    </option>
                    <option value="buying_business">
                        Cumpărare – Business
                    </option>
                </select>
            </div>

            {/* Reports table */}
            <Card>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-gray-50">
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        ID
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Tip
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        URL
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Email
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Stare
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Creat
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Procesat
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Acțiuni
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {reports.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={8}
                                            className="px-4 py-8 text-center text-muted-foreground"
                                        >
                                            Nu s-au găsit rapoarte.
                                        </td>
                                    </tr>
                                )}
                                {reports.data.map((report) => (
                                    <tr
                                        key={report.id}
                                        className="border-b hover:bg-gray-50"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {report.id}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${typeBadgeColors[report.report_type] || ""}`}
                                            >
                                                {report.report_type}
                                            </span>
                                        </td>
                                        <td
                                            className="px-4 py-3"
                                            title={report.url}
                                        >
                                            <span className="text-xs">
                                                {truncate(report.url)}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            {report.email || "—"}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${statusBadgeColors[report.status] || ""}`}
                                            >
                                                {statusLabels[report.status] ||
                                                    report.status}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-xs text-muted-foreground">
                                            {new Date(
                                                report.created_at,
                                            ).toLocaleDateString()}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-muted-foreground">
                                            {report.processed_at
                                                ? new Date(
                                                      report.processed_at,
                                                  ).toLocaleDateString()
                                                : "—"}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-2">
                                                {report.status ===
                                                    "to_be_sent" && (
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className="text-xs h-7 cursor-pointer"
                                                        onClick={() =>
                                                            router.post(
                                                                `/admin/reports/${report.id}/send`,
                                                            )
                                                        }
                                                    >
                                                        Trimite
                                                    </Button>
                                                )}
                                                {report.report_url && (
                                                    <a
                                                        href={report.report_url}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="text-xs text-brand-tertiary hover:underline"
                                                    >
                                                        PDF
                                                    </a>
                                                )}
                                                <Link
                                                    href={`/admin/reports/${report.id}`}
                                                    className="text-xs text-brand-tertiary hover:underline"
                                                >
                                                    Detalii
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
