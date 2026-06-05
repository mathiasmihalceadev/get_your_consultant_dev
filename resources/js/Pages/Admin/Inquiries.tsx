import { Head, router } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { buttonVariants } from "@/Components/ui/button";
import { cn } from "@/lib/utils";
import { ContactInquiry, PaginatedData } from "@/types";
import { PaperPlaneTilt } from "@phosphor-icons/react";

const sectionClass =
    "rounded-none border border-brand-primary/10 bg-white shadow-none";
const sectionHeaderClass =
    "rounded-none border-b border-brand-primary/10 px-5 py-4";
const surfaceClass =
    "border border-brand-primary/10 bg-brand-primary/[0.02] px-4 py-4";

interface InquiryCounts {
    total: number;
    today: number;
    thisWeek: number;
}

interface InquiriesProps {
    inquiries: PaginatedData<ContactInquiry>;
    counts: InquiryCounts;
}

function truncate(str: string, len = 120): string {
    return str.length > len ? `${str.substring(0, len)}…` : str;
}

function formatDateTime(value: string): string {
    return new Date(value).toLocaleString("ro-RO", {
        day: "2-digit",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function MobileInquiryRow({ inquiry }: { inquiry: ContactInquiry }) {
    return (
        <div className="space-y-4 border-b border-brand-primary/10 px-5 py-4 last:border-b-0">
            <div className="space-y-1">
                <p className="text-sm font-medium text-brand-primary">
                    Mesaj #{inquiry.id}
                </p>
                <h2 className="text-base font-semibold leading-6 text-brand-primary">
                    {inquiry.subject}
                </h2>
                <p className="text-sm text-brand-primary/60">
                    Limbă: {inquiry.locale}
                </p>
            </div>

            <div className="grid gap-3 sm:grid-cols-2">
                <div className={surfaceClass}>
                    <p className="text-sm text-brand-primary/60">De la</p>
                    <p className="mt-1 text-sm font-medium text-brand-primary">
                        {inquiry.name}
                    </p>
                </div>

                <div className={surfaceClass}>
                    <p className="text-sm text-brand-primary/60">Creat</p>
                    <p className="mt-1 text-sm font-medium text-brand-primary">
                        {formatDateTime(inquiry.created_at)}
                    </p>
                </div>
            </div>

            <div className={surfaceClass}>
                <p className="text-sm text-brand-primary/60">Email</p>
                <p className="mt-1 break-all text-sm text-brand-primary/82">
                    {inquiry.email}
                </p>
            </div>

            <div className="border border-brand-primary/10 bg-white px-4 py-4">
                <p className="text-sm text-brand-primary/60">Mesaj</p>
                <p className="mt-2 text-sm leading-6 text-brand-primary/78">
                    {truncate(inquiry.message, 260)}
                </p>
            </div>

            <a
                href={`mailto:${inquiry.email}?subject=${encodeURIComponent(`Re: ${inquiry.subject}`)}`}
                className={cn(
                    buttonVariants({ size: "lg" }),
                    "h-11 rounded-none bg-brand-primary text-white hover:bg-brand-primary/92",
                )}
            >
                <PaperPlaneTilt size={16} weight="duotone" />
                Răspunde
            </a>
        </div>
    );
}

export default function Inquiries({ inquiries, counts }: InquiriesProps) {
    const summaryCards = [
        {
            label: "Total mesaje",
            value: counts.total,
            tone: "text-brand-primary",
        },
        { label: "Astăzi", value: counts.today, tone: "text-brand-secondary" },
        {
            label: "Ultimele 7 zile",
            value: counts.thisWeek,
            tone: "text-brand-tertiary",
        },
    ];

    return (
        <AdminLayout>
            <Head title="Mesaje" />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 border-b border-brand-primary/10 pb-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-brand-primary">
                            Mesaje
                        </h1>
                        <p className="mt-1 text-sm text-brand-primary/60">
                            Mesajele primite prin formularul public.
                        </p>
                    </div>

                    <p className="text-sm text-brand-primary/60">
                        Total: {counts.total}
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
                                Lista mesajelor
                            </CardTitle>
                            <p className="text-sm text-brand-primary/60">
                                Afișate acum: {inquiries.data.length}
                            </p>
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <div className="lg:hidden">
                            {inquiries.data.length === 0 ? (
                                <div className="px-5 py-10 text-center text-sm text-brand-primary/60">
                                    Nu există mesaje momentan.
                                </div>
                            ) : (
                                inquiries.data.map((inquiry) => (
                                    <MobileInquiryRow
                                        key={inquiry.id}
                                        inquiry={inquiry}
                                    />
                                ))
                            )}
                        </div>

                        <div className="hidden overflow-x-auto lg:block">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-brand-primary/10 bg-brand-primary/2">
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            ID
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Nume
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Email
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Subiect
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Mesaj
                                        </th>
                                        <th className="px-5 py-3 text-left text-sm font-medium text-brand-primary/72">
                                            Limbă
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
                                    {inquiries.data.length === 0 && (
                                        <tr>
                                            <td
                                                colSpan={8}
                                                className="px-5 py-10 text-center text-sm text-brand-primary/60"
                                            >
                                                Nu există mesaje momentan.
                                            </td>
                                        </tr>
                                    )}

                                    {inquiries.data.map((inquiry) => (
                                        <tr
                                            key={inquiry.id}
                                            className="border-b border-brand-primary/10 align-top transition-colors hover:bg-brand-primary/3"
                                        >
                                            <td className="px-5 py-4 text-sm font-medium text-brand-primary">
                                                {inquiry.id}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/80">
                                                {inquiry.name}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/80">
                                                {inquiry.email}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/80">
                                                {inquiry.subject}
                                            </td>
                                            <td
                                                className="px-5 py-4 text-sm leading-6 text-brand-primary/70"
                                                title={inquiry.message}
                                            >
                                                {truncate(inquiry.message)}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/68">
                                                {inquiry.locale}
                                            </td>
                                            <td className="px-5 py-4 text-sm text-brand-primary/68">
                                                {formatDateTime(
                                                    inquiry.created_at,
                                                )}
                                            </td>
                                            <td className="px-5 py-4 text-sm">
                                                <a
                                                    href={`mailto:${inquiry.email}?subject=${encodeURIComponent(`Re: ${inquiry.subject}`)}`}
                                                    className={cn(
                                                        buttonVariants({
                                                            variant: "outline",
                                                            size: "sm",
                                                        }),
                                                        "h-9 rounded-none border-brand-primary/15 px-3 text-brand-primary hover:bg-brand-primary/4",
                                                    )}
                                                >
                                                    <PaperPlaneTilt
                                                        size={15}
                                                        weight="duotone"
                                                    />
                                                    Răspunde
                                                </a>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {inquiries.links && inquiries.links.length > 3 && (
                            <div className="flex flex-wrap items-center justify-center gap-2 border-t border-brand-primary/10 px-5 py-4">
                                {inquiries.links.map((link, index) => (
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
