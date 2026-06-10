import { Head, Link, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button, buttonVariants } from "@/Components/ui/button";
import { Alert, AlertDescription } from "@/Components/ui/alert";
import {
    FilePdf,
    PaperPlaneTilt,
    ArrowLeft,
    EnvelopeSimple,
    ClockCountdown,
    LinkSimple,
    Fingerprint,
} from "@phosphor-icons/react";
import { Report, ReportType, ReportStatus } from "@/types";
import { cn } from "@/lib/utils";

const statusBadgeColors: Record<ReportStatus, string> = {
    not_accessible: "bg-yellow-100 text-yellow-800",
    awaiting_payment: "bg-amber-100 text-amber-800",
    payment_processing: "bg-sky-100 text-sky-800",
    payment_cancelled: "bg-amber-100 text-amber-800",
    payment_failed: "bg-red-100 text-red-800",
    test_completed: "bg-emerald-100 text-emerald-800",
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

const typeLabels: Record<ReportType, string> = {
    rental_living: "Închiriere – Rezidențial",
    rental_business: "Închiriere – Business",
    buying_living: "Cumpărare – Rezidențial",
    buying_business: "Cumpărare – Business",
};

const statusLabels: Record<ReportStatus, string> = {
    not_accessible: "Inaccesibil",
    awaiting_payment: "Așteaptă plata",
    payment_processing: "Plată în confirmare",
    payment_cancelled: "Plată anulată",
    payment_failed: "Plată eșuată",
    test_completed: "Test finalizat",
    pending: "În așteptare",
    to_be_sent: "De trimis",
    sent: "Trimis",
    error: "Eroare",
};

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

function affiliateLabel(report: Report): string {
    return report.affiliate_ref || report.latest_purchase?.affiliate_ref || "-";
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

function feedbackActionLabel(report: Report): string {
    return report.feedback_sent_at ? "Retrimite feedback" : "Trimite feedback";
}

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

            <div className="max-w-2xl space-y-4 lg:hidden">
                <Card className="rounded-none border border-brand-primary/10 bg-white shadow-none">
                    <CardContent className="space-y-5 px-5 py-5">
                        <div>
                            <p className="text-sm text-brand-primary/60">
                                {typeLabels[report.report_type] ||
                                    report.report_type}
                            </p>
                            <h1 className="mt-1 text-2xl font-semibold text-brand-primary">
                                Raport #{report.id}
                            </h1>
                            <p className="mt-2 text-sm text-brand-primary/68">
                                Status:{" "}
                                {statusLabels[report.status] || report.status}
                            </p>
                            {report.is_test && (
                                <p className="mt-2 text-sm text-brand-primary/60">
                                    Flux intern de test pentru Stripe +
                                    SmartBill
                                </p>
                            )}
                        </div>

                        <div className="grid grid-cols-2 gap-3">
                            <div className="border border-brand-primary/10 bg-brand-primary/2 px-4 py-4">
                                <p className="text-sm text-brand-primary/60">
                                    Creat
                                </p>
                                <p className="mt-1 text-sm font-medium text-brand-primary">
                                    {formatDateTime(report.created_at)}
                                </p>
                            </div>
                            <div className="border border-brand-primary/10 bg-brand-primary/2 px-4 py-4">
                                <p className="text-sm text-brand-primary/60">
                                    Procesat
                                </p>
                                <p className="mt-1 text-sm font-medium text-brand-primary">
                                    {formatDateTime(report.processed_at)}
                                </p>
                            </div>
                            <div className="border border-brand-primary/10 bg-brand-primary/2 px-4 py-4">
                                <p className="text-sm text-brand-primary/60">
                                    Feedback email
                                </p>
                                <p className="mt-1 text-sm font-medium text-brand-primary">
                                    {formatDateTime(report.feedback_sent_at)}
                                </p>
                            </div>
                            <div className="border border-brand-primary/10 bg-brand-primary/2 px-4 py-4">
                                <p className="text-sm text-brand-primary/60">
                                    Feedback primit
                                </p>
                                <p className="mt-1 text-sm font-medium text-brand-primary">
                                    {report.feedback
                                        ? formatDateTime(
                                              report.feedback.submitted_at,
                                          )
                                        : "-"}
                                </p>
                            </div>
                            <div className="border border-brand-primary/10 bg-brand-primary/2 px-4 py-4">
                                <p className="text-sm text-brand-primary/60">
                                    Afiliat
                                </p>
                                <p className="mt-1 break-all text-sm font-medium text-brand-primary">
                                    {affiliateLabel(report)}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {report.error_message && (
                    <Alert variant="destructive">
                        <AlertDescription>
                            {report.error_message}
                        </AlertDescription>
                    </Alert>
                )}

                {report.feedback && (
                    <Card className="rounded-none border border-brand-primary/10 bg-white shadow-none">
                        <CardContent className="space-y-3 px-5 py-5 text-sm text-brand-primary/82">
                            <div className="font-semibold text-brand-primary">
                                Feedback client
                            </div>
                            <p>
                                <span className="text-brand-primary/56">
                                    Nota:
                                </span>{" "}
                                {report.feedback.rating}/10
                            </p>
                            <p>
                                <span className="text-brand-primary/56">
                                    Recomanda:
                                </span>{" "}
                                {report.feedback.would_recommend ? "DA" : "NU"}
                            </p>
                            <p>
                                <span className="text-brand-primary/56">
                                    Cea mai utila informatie:
                                </span>{" "}
                                {report.feedback.most_useful_info}
                            </p>
                            {report.feedback.wanted_extra && (
                                <p>
                                    <span className="text-brand-primary/56">
                                        In plus:
                                    </span>{" "}
                                    {report.feedback.wanted_extra}
                                </p>
                            )}
                            {report.feedback.trust_improvement && (
                                <p>
                                    <span className="text-brand-primary/56">
                                        Incredere:
                                    </span>{" "}
                                    {report.feedback.trust_improvement}
                                </p>
                            )}
                        </CardContent>
                    </Card>
                )}

                <Card className="border border-brand-primary/10 shadow-[0_14px_34px_rgba(20,20,43,0.06)]">
                    <CardContent className="space-y-4 p-4">
                        <div className="rounded-2xl border border-brand-primary/8 bg-brand-primary/3 p-4">
                            <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-neutral">
                                <LinkSimple size={14} />
                                URL proprietate
                            </div>
                            <p className="mt-3 break-all text-sm leading-6 text-brand-primary">
                                {report.url}
                            </p>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div className="rounded-2xl border border-brand-primary/8 bg-white p-4">
                                <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-neutral">
                                    <EnvelopeSimple size={14} />
                                    Email
                                </div>
                                <p className="mt-3 break-all text-sm leading-6 text-brand-primary/82">
                                    {report.email || "—"}
                                </p>
                            </div>

                            <div className="rounded-2xl border border-brand-primary/8 bg-white p-4">
                                <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-neutral">
                                    <ClockCountdown size={14} />
                                    Status
                                </div>
                                <p className="mt-3 text-sm font-medium text-brand-primary/82">
                                    {statusLabels[report.status] ||
                                        report.status}
                                </p>
                            </div>

                            <div className="rounded-2xl border border-brand-primary/8 bg-white p-4">
                                <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-neutral">
                                    <Fingerprint size={14} />
                                    Afiliat
                                </div>
                                <p className="mt-3 break-all text-sm font-medium text-brand-primary/82">
                                    {affiliateLabel(report)}
                                </p>
                            </div>
                        </div>

                        {report.page_token && (
                            <div className="rounded-2xl border border-brand-primary/8 bg-white p-4">
                                <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-neutral">
                                    <Fingerprint size={14} />
                                    Page Token
                                </div>
                                <p className="mt-3 break-all font-mono text-xs leading-6 text-brand-primary/74">
                                    {report.page_token}
                                </p>
                            </div>
                        )}

                        {report.latest_purchase && (
                            <div className="rounded-2xl border border-brand-primary/8 bg-white p-4">
                                <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand-neutral">
                                    <ClockCountdown size={14} />
                                    Stripe + SmartBill
                                </div>

                                <div className="mt-3 grid gap-3 text-sm text-brand-primary/82">
                                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <span className="text-brand-primary/56">
                                                Purchase status:
                                            </span>
                                            <p className="mt-1 font-medium">
                                                {report.latest_purchase.status}
                                            </p>
                                        </div>
                                        <div>
                                            <span className="text-brand-primary/56">
                                                Payment intent:
                                            </span>
                                            <p className="mt-1 break-all font-medium">
                                                {report.latest_purchase
                                                    .stripe_payment_intent_id ||
                                                    "—"}
                                            </p>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <span className="text-brand-primary/56">
                                                Checkout session:
                                            </span>
                                            <p className="mt-1 break-all font-medium">
                                                {report.latest_purchase
                                                    .stripe_checkout_session_id ||
                                                    "—"}
                                            </p>
                                        </div>
                                        <div>
                                            <span className="text-brand-primary/56">
                                                Paid at:
                                            </span>
                                            <p className="mt-1 font-medium">
                                                {formatDateTime(
                                                    report.latest_purchase
                                                        .paid_at,
                                                )}
                                            </p>
                                        </div>
                                    </div>

                                    {report.latest_purchase
                                        .smart_bill_invoice && (
                                        <div className="rounded-2xl border border-brand-primary/8 bg-brand-primary/2 p-4">
                                            <div className="grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <span className="text-brand-primary/56">
                                                        SmartBill invoice:
                                                    </span>
                                                    <p className="mt-1 font-medium">
                                                        {report.latest_purchase
                                                            .smart_bill_invoice
                                                            .invoice_series &&
                                                        report.latest_purchase
                                                            .smart_bill_invoice
                                                            .invoice_number
                                                            ? `${report.latest_purchase.smart_bill_invoice.invoice_series}${report.latest_purchase.smart_bill_invoice.invoice_number}`
                                                            : "În curs / indisponibil"}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span className="text-brand-primary/56">
                                                        SmartBill status:
                                                    </span>
                                                    <p className="mt-1 font-medium">
                                                        {
                                                            report
                                                                .latest_purchase
                                                                .smart_bill_invoice
                                                                .status
                                                        }{" "}
                                                        /{" "}
                                                        {
                                                            report
                                                                .latest_purchase
                                                                .smart_bill_invoice
                                                                .payment_status
                                                        }
                                                    </p>
                                                </div>
                                            </div>

                                            {report.latest_purchase
                                                .smart_bill_invoice
                                                .file_url && (
                                                <a
                                                    href={
                                                        report.latest_purchase
                                                            .smart_bill_invoice
                                                            .file_url
                                                    }
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className={cn(
                                                        buttonVariants({
                                                            variant: "outline",
                                                            size: "sm",
                                                        }),
                                                        "mt-3 h-9 border-brand-primary/15 text-brand-primary",
                                                    )}
                                                >
                                                    Vezi documentul SmartBill
                                                </a>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        <div className="flex flex-col gap-2 pt-1">
                            {!report.is_test &&
                                report.status === "to_be_sent" && (
                                    <Button
                                        onClick={() =>
                                            router.post(
                                                `/admin/reports/${report.id}/send`,
                                            )
                                        }
                                        className="h-11 w-full gap-2 bg-brand-primary text-white hover:bg-brand-primary/90"
                                    >
                                        <PaperPlaneTilt size={18} />
                                        Trimite Raport
                                    </Button>
                                )}

                            {report.report_url && (
                                <a
                                    href={`/admin/reports/${report.id}/pdf`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className={cn(
                                        buttonVariants({
                                            variant: "outline",
                                            size: "lg",
                                        }),
                                        "h-11 w-full border-brand-primary/15 text-brand-primary",
                                    )}
                                >
                                    <FilePdf
                                        size={18}
                                        weight="fill"
                                        className="text-red-600"
                                    />
                                    Vezi PDF
                                </a>
                            )}

                            {canSendFeedbackEmail(report) && (
                                <Button
                                    onClick={() =>
                                        router.post(
                                            `/admin/reports/${report.id}/feedback/send`,
                                        )
                                    }
                                    variant="outline"
                                    className="h-11 w-full gap-2 border-brand-primary/15 text-brand-primary"
                                >
                                    <EnvelopeSimple size={18} />
                                    {feedbackActionLabel(report)}
                                </Button>
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div className="hidden max-w-2xl lg:block">
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
                            <div>
                                <span className="text-muted-foreground">
                                    Afiliat:
                                </span>
                                <p className="break-all font-medium">
                                    {affiliateLabel(report)}
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
                                <div>
                                    <span className="text-muted-foreground">
                                        Feedback email:
                                    </span>
                                    <p className="font-medium">
                                        {formatDateTime(
                                            report.feedback_sent_at,
                                        )}
                                    </p>
                                </div>
                                <div>
                                    <span className="text-muted-foreground">
                                        Feedback primit:
                                    </span>
                                    <p className="font-medium">
                                        {report.feedback
                                            ? formatDateTime(
                                                  report.feedback.submitted_at,
                                              )
                                            : "-"}
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

                        {report.feedback && (
                            <div className="rounded-2xl border border-brand-primary/8 bg-brand-primary/2 p-4 text-sm text-brand-primary/82">
                                <div className="mb-3 font-semibold text-brand-primary">
                                    Feedback client
                                </div>
                                <div className="space-y-3">
                                    <p>
                                        <span className="text-brand-primary/56">
                                            Nota:
                                        </span>{" "}
                                        {report.feedback.rating}/10
                                    </p>
                                    <p>
                                        <span className="text-brand-primary/56">
                                            Recomanda:
                                        </span>{" "}
                                        {report.feedback.would_recommend
                                            ? "DA"
                                            : "NU"}
                                    </p>
                                    <p>
                                        <span className="text-brand-primary/56">
                                            Cea mai utila informatie:
                                        </span>{" "}
                                        {report.feedback.most_useful_info}
                                    </p>
                                    {report.feedback.wanted_extra && (
                                        <p>
                                            <span className="text-brand-primary/56">
                                                In plus:
                                            </span>{" "}
                                            {report.feedback.wanted_extra}
                                        </p>
                                    )}
                                    {report.feedback.trust_improvement && (
                                        <p>
                                            <span className="text-brand-primary/56">
                                                Incredere:
                                            </span>{" "}
                                            {
                                                report.feedback
                                                    .trust_improvement
                                            }
                                        </p>
                                    )}
                                </div>
                            </div>
                        )}

                        <div className="flex items-center gap-3 pt-2">
                            {report.report_url && (
                                <a
                                    href={`/admin/reports/${report.id}/pdf`}
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
                            {!report.is_test &&
                                report.status === "to_be_sent" && (
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

                            {canSendFeedbackEmail(report) && (
                                <Button
                                    onClick={() =>
                                        router.post(
                                            `/admin/reports/${report.id}/feedback/send`,
                                        )
                                    }
                                    variant="outline"
                                    className="gap-2 cursor-pointer"
                                >
                                    <EnvelopeSimple size={18} />
                                    {feedbackActionLabel(report)}
                                </Button>
                            )}
                        </div>

                        {report.is_test &&
                            report.latest_purchase?.smart_bill_invoice
                                ?.file_url && (
                                <a
                                    href={
                                        report.latest_purchase
                                            .smart_bill_invoice.file_url
                                    }
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <Button
                                        variant="outline"
                                        className="gap-2 cursor-pointer"
                                    >
                                        Vezi documentul SmartBill
                                    </Button>
                                </a>
                            )}
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
