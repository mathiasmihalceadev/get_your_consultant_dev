import { Head, Link, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import { Button } from "@/Components/ui/button";
import { Alert, AlertDescription } from "@/Components/ui/alert";
import { FilePdf, PaperPlaneTilt, ArrowLeft } from "@phosphor-icons/react";
import { Report, ReportType, ReportStatus } from "@/types";

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

export default function ReportDetail({ report }: { report: Report }) {
    return (
        <AdminLayout>
            <Head title={`Report #${report.id}`} />

            <Link
                href="/admin/dashboard"
                className="inline-flex items-center gap-1 text-sm text-brand-neutral hover:text-brand-primary mb-6 transition-colors"
            >
                <ArrowLeft size={16} />
                Înapoi la Panou
            </Link>

            <div className="max-w-2xl">
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2 mb-2">
                            <span
                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeBadgeColors[report.report_type] || ""}`}
                            >
                                {report.report_type}
                            </span>
                            <span
                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusBadgeColors[report.status] || ""}`}
                            >
                                {statusLabels[report.status] || report.status}
                            </span>
                        </div>
                        <CardTitle>Raport #{report.id}</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-1 gap-3 text-sm">
                            <div>
                                <span className="text-muted-foreground">
                                    URL:
                                </span>
                                <p className="break-all font-medium">
                                    {report.url}
                                </p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">
                                    Email:
                                </span>
                                <p className="font-medium">
                                    {report.email || "—"}
                                </p>
                            </div>
                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <span className="text-muted-foreground">
                                        Creat:
                                    </span>
                                    <p className="font-medium">
                                        {new Date(
                                            report.created_at,
                                        ).toLocaleString()}
                                    </p>
                                </div>
                                <div>
                                    <span className="text-muted-foreground">
                                        Procesat:
                                    </span>
                                    <p className="font-medium">
                                        {report.processed_at
                                            ? new Date(
                                                  report.processed_at,
                                              ).toLocaleString()
                                            : "—"}
                                    </p>
                                </div>
                            </div>
                            {report.page_token && (
                                <div>
                                    <span className="text-muted-foreground">
                                        Page Token:
                                    </span>
                                    <p className="font-mono text-xs break-all">
                                        {report.page_token}
                                    </p>
                                </div>
                            )}
                        </div>

                        {report.error_message && (
                            <Alert variant="destructive">
                                <AlertDescription>
                                    {report.error_message}
                                </AlertDescription>
                            </Alert>
                        )}

                        <div className="flex items-center gap-3 pt-2">
                            {report.report_url && (
                                <a
                                    href={report.report_url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <Button
                                        variant="outline"
                                        className="gap-2 cursor-pointer"
                                    >
                                        <FilePdf
                                            size={18}
                                            weight="fill"
                                            className="text-red-600"
                                        />
                                        Vezi PDF
                                    </Button>
                                </a>
                            )}
                            {report.status === "to_be_sent" && (
                                <Button
                                    onClick={() =>
                                        router.post(
                                            `/admin/reports/${report.id}/send`,
                                        )
                                    }
                                    className="gap-2 bg-brand-primary hover:bg-brand-primary/90 text-white cursor-pointer"
                                >
                                    <PaperPlaneTilt size={18} />
                                    Trimite Raport
                                </Button>
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
