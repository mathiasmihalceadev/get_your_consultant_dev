import { Head, Link, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button, buttonVariants } from "@/Components/ui/button";
import { cn } from "@/lib/utils";
import { PaginatedData, Report } from "@/types";
import { ArrowSquareOut, PaperPlaneTilt } from "@phosphor-icons/react";

const sectionClass =
    "rounded-none border border-brand-primary/10 bg-white shadow-none";
const sectionHeaderClass =
    "rounded-none border-b border-brand-primary/10 px-5 py-4";
const outlineButtonClass =
    "rounded-none border-brand-primary/15 text-brand-primary hover:bg-brand-primary/4";

interface FeedbackCounts {
    sent: number;
    received: number;
    pending: number;
}

interface FeedbacksProps {
    reports: PaginatedData<Report>;
    counts: FeedbackCounts;
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

function truncate(value: string | null, length = 120): string {
    if (!value) {
        return "-";
    }

    return value.length > length ? `${value.slice(0, length)}...` : value;
}

function recommendLabel(report: Report): string {
    if (!report.feedback) {
        return "-";
    }

    return report.feedback.would_recommend ? "DA" : "NU";
}

function resendFeedback(report: Report): void {
    router.post(`/admin/reports/${report.id}/feedback/send`, {}, {
        preserveScroll: true,
    });
}

function MobileFeedbackRow({ report }: { report: Report }) {
    return (
        <div className="space-y-4 border-b border-brand-primary/10 px-5 py-4 last:border-b-0">
            <div className="space-y-1">
                <p className="text-sm font-medium text-brand-primary">
                    Raport #{report.id}
                </p>
                <p className="break-all text-sm text-brand-primary/68">
                    {report.email || "-"}
                </p>
            </div>

            <div className="grid gap-3 sm:grid-cols-2">
                <div className="border border-brand-primary/10 bg-brand-primary/[0.02] px-4 py-4">
                    <p className="text-sm text-brand-primary/60">
                        Feedback email
                    </p>
                    <p className="mt-1 text-sm font-medium text-brand-primary">
                        {formatDateTime(report.feedback_sent_at)}
                    </p>
                </div>
                <div className="border border-brand-primary/10 bg-brand-primary/[0.02] px-4 py-4">
                    <p className="text-sm text-brand-primary/60">
                        Feedback primit
                    </p>
                    <p className="mt-1 text-sm font-medium text-brand-primary">
                        {report.feedback
                            ? formatDateTime(report.feedback.submitted_at)
                            : "-"}
                    </p>
                </div>
            </div>

            {report.feedback ? (
                <div className="space-y-3 border border-brand-primary/10 bg-white px-4 py-4 text-sm text-brand-primary/78">
                    <p>
                        <span className="font-medium text-brand-primary">
                            Nota:
                        </span>{" "}
                        {report.feedback.rating}/10
                    </p>
                    <p>
                        <span className="font-medium text-brand-primary">
                            Recomanda:
                        </span>{" "}
                        {recommendLabel(report)}
                    </p>
                    <p>
                        <span className="font-medium text-brand-primary">
                            Cea mai utila informatie:
                        </span>{" "}
                        {report.feedback.most_useful_info}
                    </p>
                    {report.feedback.wanted_extra && (
                        <p>
                            <span className="font-medium text-brand-primary">
                                In plus:
                            </span>{" "}
                            {report.feedback.wanted_extra}
                        </p>
                    )}
                    {report.feedback.trust_improvement && (
                        <p>
                            <span className="font-medium text-brand-primary">
                                Incredere:
                            </span>{" "}
                            {report.feedback.trust_improvement}
                        </p>
                    )}
                </div>
            ) : (
                <p className="text-sm text-brand-primary/60">
                    Clientul nu a completat inca formularul.
                </p>
            )}

            <div className="flex flex-wrap gap-2">
                <Button
                    size="sm"
                    variant="outline"
                    className={outlineButtonClass}
                    onClick={() => resendFeedback(report)}
                >
                    <PaperPlaneTilt size={15} weight="duotone" />
                    Retrimite feedback
                </Button>
                <Link
                    href={`/admin/reports/${report.id}`}
                    className={cn(
                        buttonVariants({ variant: "outline", size: "sm" }),
                        outlineButtonClass,
                        "h-9 px-3",
                    )}
                >
                    <ArrowSquareOut size={15} weight="duotone" />
                    Detalii
                </Link>
            </div>
        </div>
    );
}

export default function Feedbacks({ reports, counts }: FeedbacksProps) {
    const summaryCards = [
        {
            label: "Emailuri feedback trimise",
            value: counts.sent,
            tone: "text-brand-primary",
        },
        {
            label: "Feedback primit",
            value: counts.received,
            tone: "text-emerald-700",
        },
        {
            label: "Fara raspuns",
            value: counts.pending,
            tone: "text-brand-secondary",
        },
    ];

    return (
        <AdminLayout>
            <Head title="Feedback" />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 border-b border-brand-primary/10 pb-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-brand-primary">
                            Feedback
                        </h1>
                        <p className="mt-1 text-sm text-brand-primary/60">
                            Rapoarte pentru care a fost trimis formularul de
                            feedback si raspunsurile primite.
                        </p>
                    </div>

                    <p className="text-sm text-brand-primary/60">
                        Total trimise: {counts.sent}
                    </p>
                </div>

                <div className="grid gap-4 sm:grid-cols-3">
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
                                Feedback trimis
                            </CardTitle>
                            <p className="text-sm text-brand-primary/60">
                                Afisate acum: {reports.data.length}
                            </p>
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <div className="lg:hidden">
                            {reports.data.length === 0 ? (
                                <div className="px-5 py-10 text-center text-sm text-brand-primary/60">
                                    Nu exista emailuri de feedback trimise.
                                </div>
                            ) : (
                                reports.data.map((report) => (
                                    <MobileFeedbackRow
                                        key={report.id}
                                        report={report}
                                    />
                                ))
                            )}
                        </div>

                        <div className="hidden overflow-x-auto lg:block">
                            <table className="min-w-[1200px] w-full text-sm">
                                <thead>
                                    <tr className="border-b border-brand-primary/10 bg-brand-primary/2">
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Raport
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Email
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Feedback email
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Feedback primit
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Nota
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Recomanda
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Cea mai utila informatie
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Actiuni
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {reports.data.length === 0 && (
                                        <tr>
                                            <td
                                                colSpan={8}
                                                className="px-5 py-10 text-center text-sm text-brand-primary/60"
                                            >
                                                Nu exista emailuri de feedback
                                                trimise.
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
                                                <p
                                                    className="mt-1 max-w-[18rem] truncate text-sm text-brand-primary/64"
                                                    title={report.url}
                                                >
                                                    {report.url}
                                                </p>
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/70">
                                                {report.email || "-"}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/68">
                                                {formatDateTime(
                                                    report.feedback_sent_at,
                                                )}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/68">
                                                {report.feedback
                                                    ? formatDateTime(
                                                          report.feedback
                                                              .submitted_at,
                                                      )
                                                    : "-"}
                                            </td>
                                            <td className="px-5 py-4 text-sm font-medium text-brand-primary">
                                                {report.feedback
                                                    ? `${report.feedback.rating}/10`
                                                    : "-"}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/70">
                                                {recommendLabel(report)}
                                            </td>
                                            <td
                                                className="px-5 py-4 text-sm leading-6 text-brand-primary/70"
                                                title={
                                                    report.feedback
                                                        ?.most_useful_info || ""
                                                }
                                            >
                                                {truncate(
                                                    report.feedback
                                                        ?.most_useful_info,
                                                    110,
                                                )}
                                            </td>
                                            <td className="px-5 py-4">
                                                <div className="flex flex-wrap gap-2">
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className={
                                                            outlineButtonClass
                                                        }
                                                        onClick={() =>
                                                            resendFeedback(
                                                                report,
                                                            )
                                                        }
                                                    >
                                                        <PaperPlaneTilt
                                                            size={15}
                                                            weight="duotone"
                                                        />
                                                        Retrimite
                                                    </Button>
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
                                                        <ArrowSquareOut
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
