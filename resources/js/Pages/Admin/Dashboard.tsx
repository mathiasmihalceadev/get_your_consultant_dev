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
    purchase: "bg-violet-100 text-violet-800",
    rental: "bg-teal-100 text-teal-800",
    commercial: "bg-amber-100 text-amber-800",
};

const statusLabels: Record<ReportStatus, string> = {
    not_accessible: "Not Accessible",
    pending: "Pending",
    to_be_sent: "To Be Sent",
    sent: "Sent",
    error: "Error",
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
            <Head title="Admin Dashboard" />

            <h1 className="text-2xl font-bold text-[#0a0a0a] mb-6">
                Dashboard
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
                        label: "Pending",
                        count: counts.pending,
                        color: "text-blue-600",
                    },
                    {
                        label: "To Be Sent",
                        count: counts.to_be_sent,
                        color: "text-orange-600",
                    },
                    {
                        label: "Sent",
                        count: counts.sent,
                        color: "text-green-600",
                    },
                    {
                        label: "Error",
                        count: counts.error,
                        color: "text-red-600",
                    },
                    {
                        label: "Not Accessible",
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
                    <option value="">All Types</option>
                    <option value="purchase">Purchase</option>
                    <option value="rental">Rental</option>
                    <option value="commercial">Commercial</option>
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
                                        Type
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        URL
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Email
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Status
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Created
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Processed
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        Actions
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
                                            No reports found.
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
                                                        Send
                                                    </Button>
                                                )}
                                                {report.report_url && (
                                                    <a
                                                        href={`/storage/${report.report_url}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="text-xs text-[#1a56db] hover:underline"
                                                    >
                                                        PDF
                                                    </a>
                                                )}
                                                <Link
                                                    href={`/admin/reports/${report.id}`}
                                                    className="text-xs text-[#1a56db] hover:underline"
                                                >
                                                    Details
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
                                            ? "bg-[#1a56db] text-white"
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
