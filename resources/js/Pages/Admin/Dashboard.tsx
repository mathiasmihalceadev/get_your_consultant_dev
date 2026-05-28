import { Head, Link, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent } from "@/Components/ui/card";
import { Button, buttonVariants } from "@/Components/ui/button";
import { Report, ReportType, ReportStatus, PaginatedData } from "@/types";
import { cn } from "@/lib/utils";
import { CaretDown, Eye, FilePdf, PaperPlaneTilt } from "@phosphor-icons/react";

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
    test_completed: {
        label: "Test finalizat",
        bg: "bg-emerald-50",
        text: "text-emerald-700",
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

function emailActionLabel(report: Report): string {
    return report.status === "sent" ? "Retrimite" : "Trimite";
}

function truncate(str: string | null, len = 52): string {
    if (!str) {
        return "";
    }

    return str.length > len ? `${str.substring(0, len)}…` : str;
}

function formatDateTime(value: string | null): string {
    if (!value) {
        return "—";
    }

    return new Date(value).toLocaleString("ro-RO", {
        day: "2-digit",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function ActionableReportCard({ report }: { report: Report }) {
    return (
        <Card className="min-w-70 border border-brand-primary/10 bg-white shadow-[0_16px_34px_rgba(52,48,106,0.08)] lg:min-w-0">
            <CardContent className="p-4">
                <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-secondary">
                            Gata de trimis
                        </p>
                        <h2 className="mt-1 text-sm font-semibold leading-5 text-brand-primary">
                            {typeConfig[report.report_type]?.label ??
                                report.report_type}
                        </h2>
                    </div>

                    <span
                        className={`inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium ${statusConfig[report.status].bg} ${statusConfig[report.status].text}`}
                    >
                        {statusConfig[report.status].label}
                    </span>
                </div>

                <p className="mt-3 text-xs leading-5 text-brand-primary/70">
                    #{report.id} • {formatDateTime(report.created_at)}
                </p>
                {report.is_test && (
                    <p className="mt-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-secondary">
                        Flux de test facturare
                    </p>
                )}
                <p className="mt-2 break-all text-sm leading-6 text-brand-primary/80">
                    {truncate(report.url, 88)}
                </p>

                <div className="mt-4 flex gap-2">
                    <Button
                        size="sm"
                        className="h-8 flex-1 bg-brand-primary text-white hover:bg-brand-primary/92"
                        onClick={() =>
                            router.post(`/admin/reports/${report.id}/send`)
                        }
                    >
                        <PaperPlaneTilt size={15} weight="duotone" />
                        {emailActionLabel(report)}
                    </Button>

                    <Link
                        href={`/admin/reports/${report.id}`}
                        className={cn(
                            buttonVariants({ variant: "outline", size: "sm" }),
                            "h-8 border-brand-primary/15 text-brand-primary",
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
        <div className="border-b border-brand-primary/8 px-4 py-4 last:border-b-0">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-neutral">
                        Raport #{report.id}
                    </p>
                    <h3 className="mt-1 text-sm font-semibold leading-5 text-brand-primary">
                        {typeConfig[report.report_type]?.label ??
                            report.report_type}
                    </h3>
                </div>

                <span
                    className={`inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium ${statusConfig[report.status].bg} ${statusConfig[report.status].text}`}
                >
                    {statusConfig[report.status].label}
                </span>
            </div>

            <p className="mt-2 text-xs text-brand-primary/62">
                {formatDateTime(report.created_at)}
            </p>
            {report.is_test && (
                <p className="mt-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-secondary">
                    Flux de test facturare
                </p>
            )}
            <p className="mt-2 break-all text-sm leading-6 text-brand-primary/78">
                {truncate(report.url, 120)}
            </p>

            <div className="mt-3 flex flex-wrap items-center gap-2 text-xs text-brand-primary/68">
                <span>{report.email || "Fără email"}</span>
                {report.processed_at && (
                    <span>• {formatDateTime(report.processed_at)}</span>
                )}
            </div>

            <div className="mt-4 flex flex-wrap gap-2">
                {canEmailReport(report) && (
                    <Button
                        size="sm"
                        className="h-8 bg-brand-primary text-white hover:bg-brand-primary/92"
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
                        href={report.report_url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={cn(
                            buttonVariants({ variant: "outline", size: "sm" }),
                            "h-8 border-brand-primary/15 text-brand-primary",
                        )}
                    >
                        <FilePdf size={15} weight="duotone" />
                        PDF
                    </a>
                )}

                <Link
                    href={`/admin/reports/${report.id}`}
                    className={cn(
                        buttonVariants({ variant: "outline", size: "sm" }),
                        "h-8 border-brand-primary/15 text-brand-primary",
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
    const applyStatusFilter = (value: string) => {
        const params: Record<string, string> = {};

        if (value) {
            params.status = value;
        }

        router.get("/admin/dashboard", params, { preserveState: true });
    };

    const actionableReports = reports.data.filter(
        (report) => report.status === "to_be_sent",
    );
    const statusOptions = [
        { value: "", label: "Toate statusurile" },
        ...Object.entries(statusConfig).map(([value, config]) => ({
            value,
            label: config.label,
        })),
    ];

    return (
        <AdminLayout>
            <Head title="Panou de Control" />

            <section className="mb-6">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-brand-primary/56">
                    Admin workflow
                </p>
                <div className="mt-2 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div className="max-w-2xl">
                        <h1 className="text-[2rem] leading-[1.02] font-bold tracking-[-0.04em] text-brand-primary md:text-[2.6rem]">
                            Panou de Control
                        </h1>
                        <p className="mt-2 text-sm leading-6 text-brand-primary/74 md:text-base">
                            Vezi mai întâi rapoartele gata de trimis, apoi
                            parcurge fluxul complet într-o listă unică filtrată
                            după status.
                        </p>
                    </div>

                    <div className="inline-flex w-fit items-center gap-2 rounded-full bg-brand-primary/6 px-4 py-2 text-sm font-semibold text-brand-primary/78">
                        <span>{counts.to_be_sent} de trimis</span>
                        <span className="text-brand-primary/35">•</span>
                        <span>{counts.total} în pagină</span>
                    </div>
                </div>
            </section>

            <section className="mb-6">
                <div className="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-secondary">
                            Prioritate
                        </p>
                        <h2 className="mt-1 text-lg font-semibold text-brand-primary md:text-[1.35rem]">
                            Rapoarte gata de trimis
                        </h2>
                    </div>
                    <span className="rounded-full bg-brand-secondary/10 px-3 py-1 text-xs font-semibold text-brand-secondary">
                        {counts.to_be_sent}
                    </span>
                </div>

                {actionableReports.length === 0 ? (
                    <Card className="border border-brand-primary/10 bg-white shadow-[0_14px_32px_rgba(52,48,106,0.06)]">
                        <CardContent className="px-4 py-6 text-sm text-brand-primary/68 md:px-5">
                            Nu există rapoarte gata de trimis în această pagină.
                        </CardContent>
                    </Card>
                ) : (
                    <div className="flex gap-3 overflow-x-auto pb-1 lg:grid lg:grid-cols-2 lg:overflow-visible xl:grid-cols-3">
                        {actionableReports.map((report) => (
                            <ActionableReportCard
                                key={report.id}
                                report={report}
                            />
                        ))}
                    </div>
                )}
            </section>

            <Card className="overflow-hidden border border-brand-primary/10 bg-white shadow-[0_18px_40px_rgba(52,48,106,0.08)]">
                <div className="border-b border-brand-primary/8 px-4 py-4 sm:px-5">
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/56">
                                Feed complet
                            </p>
                            <h2 className="mt-1 text-lg font-semibold text-brand-primary md:text-[1.35rem]">
                                Toate rapoartele
                            </h2>
                            <p className="mt-1 text-sm text-brand-primary/70">
                                Ordinate după timp. Filtrul de mai jos lucrează
                                doar pe status.
                            </p>
                        </div>

                        <label className="block w-full max-w-xs lg:w-64">
                            <span className="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-neutral">
                                Status
                            </span>
                            <div className="relative">
                                <select
                                    value={filters.status || ""}
                                    onChange={(event) =>
                                        applyStatusFilter(event.target.value)
                                    }
                                    className="h-11 w-full appearance-none rounded-2xl border border-brand-primary/12 bg-white px-4 pr-10 text-sm text-brand-primary outline-none transition-colors focus:border-brand-primary/28"
                                >
                                    {statusOptions.map(({ value, label }) => (
                                        <option
                                            key={value || "all"}
                                            value={value}
                                        >
                                            {label}
                                        </option>
                                    ))}
                                </select>
                                <CaretDown
                                    size={16}
                                    className="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-brand-primary/48"
                                />
                            </div>
                        </label>
                    </div>
                </div>

                <div className="lg:hidden">
                    {reports.data.length === 0 ? (
                        <div className="px-4 py-10 text-center text-sm text-brand-primary/66">
                            Nu s-au găsit rapoarte.
                        </div>
                    ) : (
                        reports.data.map((report) => (
                            <MobileReportRow key={report.id} report={report} />
                        ))
                    )}
                </div>

                <div className="hidden lg:block overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-brand-primary/8 bg-brand-primary/3">
                                <th className="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/72">
                                    Raport
                                </th>
                                <th className="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/72">
                                    Proprietate
                                </th>
                                <th className="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/72">
                                    Email
                                </th>
                                <th className="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/72">
                                    Status
                                </th>
                                <th className="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/72">
                                    Creat
                                </th>
                                <th className="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-primary/72">
                                    Acțiuni
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {reports.data.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={6}
                                        className="px-5 py-10 text-center text-sm text-brand-primary/66"
                                    >
                                        Nu s-au găsit rapoarte.
                                    </td>
                                </tr>
                            )}

                            {reports.data.map((report) => (
                                <tr
                                    key={report.id}
                                    className="border-b border-brand-primary/8 align-top transition-colors hover:bg-brand-primary/2.5"
                                >
                                    <td className="px-5 py-4">
                                        <p className="text-xs font-semibold text-brand-primary">
                                            #{report.id}
                                        </p>
                                        {report.is_test && (
                                            <p className="mt-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-secondary">
                                                Test billing
                                            </p>
                                        )}
                                        <p className="mt-1 text-sm text-brand-primary/78">
                                            {typeConfig[report.report_type]
                                                ?.label ?? report.report_type}
                                        </p>
                                    </td>
                                    <td className="px-5 py-4">
                                        <p
                                            className="max-w-[20rem] text-sm leading-6 text-brand-primary/76"
                                            title={report.url}
                                        >
                                            {truncate(report.url, 76)}
                                        </p>
                                    </td>
                                    <td className="px-5 py-4 text-sm text-brand-primary/70">
                                        {report.email || "—"}
                                    </td>
                                    <td className="px-5 py-4">
                                        <span
                                            className={`inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium ${statusConfig[report.status].bg} ${statusConfig[report.status].text}`}
                                        >
                                            {statusConfig[report.status].label}
                                        </span>
                                    </td>
                                    <td className="px-5 py-4 text-sm text-brand-primary/68">
                                        {formatDateTime(report.created_at)}
                                    </td>
                                    <td className="px-5 py-4">
                                        <div className="flex flex-wrap gap-2">
                                            {canEmailReport(report) && (
                                                <Button
                                                    size="sm"
                                                    className="h-8 bg-brand-primary text-white hover:bg-brand-primary/92"
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
                                                    {emailActionLabel(report)}
                                                </Button>
                                            )}

                                            {report.report_url && (
                                                <a
                                                    href={report.report_url}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className={cn(
                                                        buttonVariants({
                                                            variant: "outline",
                                                            size: "sm",
                                                        }),
                                                        "h-8 border-brand-primary/15 text-brand-primary",
                                                    )}
                                                >
                                                    <FilePdf
                                                        size={15}
                                                        weight="duotone"
                                                    />
                                                    PDF
                                                </a>
                                            )}

                                            <Link
                                                href={`/admin/reports/${report.id}`}
                                                className={cn(
                                                    buttonVariants({
                                                        variant: "outline",
                                                        size: "sm",
                                                    }),
                                                    "h-8 border-brand-primary/15 text-brand-primary",
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
                    <div className="flex flex-wrap items-center justify-center gap-2 border-t border-brand-primary/8 p-4">
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
                                className={`rounded-full px-3 py-1.5 text-sm ${
                                    link.active
                                        ? "bg-brand-primary text-white"
                                        : "text-brand-primary/64 hover:bg-brand-primary/6"
                                } ${!link.url ? "cursor-not-allowed opacity-50" : "cursor-pointer"}`}
                                dangerouslySetInnerHTML={{
                                    __html: link.label,
                                }}
                            />
                        ))}
                    </div>
                )}
            </Card>
        </AdminLayout>
    );
}
