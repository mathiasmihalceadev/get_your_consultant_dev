import { Head, router } from "@inertiajs/react";
import { Card, CardContent } from "@/Components/ui/card";
import { buttonVariants } from "@/Components/ui/button";
import { cn } from "@/lib/utils";
import {
    ClockCountdown,
    EnvelopeSimple,
    ListDashes,
    PaperPlaneTilt,
} from "@phosphor-icons/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { ContactInquiry, PaginatedData } from "@/types";

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

function MobileInquiryCard({ inquiry }: { inquiry: ContactInquiry }) {
    return (
        <Card className="overflow-hidden border border-brand-primary/10 bg-white shadow-[0_14px_34px_rgba(20,20,43,0.06)]">
            <CardContent className="p-4">
                <div className="flex items-start justify-between gap-3">
                    <div>
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-neutral">
                            Mesaj #{inquiry.id}
                        </p>
                        <h2 className="mt-1 text-base font-semibold leading-6 text-brand-primary">
                            {inquiry.subject}
                        </h2>
                    </div>

                    <span className="rounded-full bg-brand-primary/6 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-primary/72">
                        {inquiry.locale}
                    </span>
                </div>

                <div className="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div className="rounded-2xl border border-brand-primary/8 bg-brand-primary/3 p-3">
                        <p className="font-semibold uppercase tracking-[0.14em] text-brand-neutral">
                            De la
                        </p>
                        <p className="mt-2 text-sm font-medium text-brand-primary">
                            {inquiry.name}
                        </p>
                    </div>
                    <div className="rounded-2xl border border-brand-primary/8 bg-brand-primary/3 p-3">
                        <p className="font-semibold uppercase tracking-[0.14em] text-brand-neutral">
                            Creat
                        </p>
                        <p className="mt-2 text-sm font-medium text-brand-primary">
                            {formatDateTime(inquiry.created_at)}
                        </p>
                    </div>
                    <div className="col-span-2 rounded-2xl border border-brand-primary/8 bg-white p-3">
                        <p className="font-semibold uppercase tracking-[0.14em] text-brand-neutral">
                            Email
                        </p>
                        <p className="mt-2 break-all text-sm text-brand-primary/84">
                            {inquiry.email}
                        </p>
                    </div>
                </div>

                <div className="mt-4 rounded-2xl border border-brand-primary/8 bg-white p-4">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-neutral">
                        Mesaj
                    </p>
                    <p className="mt-3 text-sm leading-6 text-brand-primary/78">
                        {truncate(inquiry.message, 260)}
                    </p>
                </div>

                <div className="mt-4 flex gap-2">
                    <a
                        href={`mailto:${inquiry.email}?subject=${encodeURIComponent(`Re: ${inquiry.subject}`)}`}
                        className={cn(
                            buttonVariants({ size: "lg" }),
                            "h-10 w-full bg-brand-primary text-white hover:bg-brand-primary/92",
                        )}
                    >
                        <PaperPlaneTilt size={16} weight="duotone" />
                        Răspunde
                    </a>
                </div>
            </CardContent>
        </Card>
    );
}

export default function Inquiries({ inquiries, counts }: InquiriesProps) {
    const summaryCards = [
        {
            label: "Total mesaje",
            count: counts.total,
            color: "text-brand-primary",
            icon: ListDashes,
        },
        {
            label: "Astăzi",
            count: counts.today,
            color: "text-brand-secondary",
            icon: EnvelopeSimple,
        },
        {
            label: "Ultimele 7 zile",
            count: counts.thisWeek,
            color: "text-brand-tertiary",
            icon: ClockCountdown,
        },
    ];

    return (
        <AdminLayout>
            <Head title="Mesaje" />

            <h1 className="mb-6 text-2xl font-bold text-brand-primary">
                Mesaje primite
            </h1>

            <div className="mb-6 space-y-4 lg:hidden">
                <Card className="overflow-hidden border-0 bg-[linear-gradient(145deg,#34306A_0%,#4A4788_56%,#D89A4B_165%)] text-white shadow-[0_24px_60px_rgba(52,48,106,0.24)]">
                    <CardContent className="space-y-4 p-5">
                        <div>
                            <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/54">
                                Inbox rapid
                            </p>
                            <h2 className="mt-2 text-2xl font-semibold tracking-[-0.03em]">
                                Mesaje de urmărit
                            </h2>
                            <p className="mt-2 text-sm leading-6 text-white/76">
                                Vezi cine a scris, ce a cerut și răspunde direct
                                din telefon.
                            </p>
                        </div>

                        <div className="grid grid-cols-3 gap-3">
                            {summaryCards.map(({ label, count }) => (
                                <div
                                    key={label}
                                    className="rounded-3xl border border-white/10 bg-white/10 px-3 py-3 backdrop-blur"
                                >
                                    <p className="text-[11px] uppercase tracking-[0.16em] text-white/52">
                                        {label}
                                    </p>
                                    <p className="mt-2 text-2xl font-semibold text-white">
                                        {count}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                <div className="space-y-3">
                    {inquiries.data.length === 0 ? (
                        <Card className="border border-brand-primary/8 shadow-sm">
                            <CardContent className="px-4 py-10 text-center text-sm text-brand-neutral">
                                Nu există mesaje momentan.
                            </CardContent>
                        </Card>
                    ) : (
                        inquiries.data.map((inquiry) => (
                            <MobileInquiryCard
                                key={inquiry.id}
                                inquiry={inquiry}
                            />
                        ))
                    )}
                </div>

                {inquiries.links && inquiries.links.length > 3 && (
                    <Card className="border border-brand-primary/8 shadow-sm">
                        <CardContent className="flex flex-wrap items-center justify-center gap-2 p-4">
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
                                    className={`cursor-pointer rounded-full px-3 py-1.5 text-sm ${
                                        link.active
                                            ? "bg-brand-primary text-white"
                                            : "text-gray-600 hover:bg-gray-100"
                                    } ${!link.url ? "cursor-not-allowed opacity-50" : ""}`}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ))}
                        </CardContent>
                    </Card>
                )}
            </div>

            <div className="hidden mb-6 grid-cols-1 gap-4 md:grid-cols-3 lg:grid">
                {summaryCards.map(({ label, count, color, icon: Icon }) => (
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
                            <div className="mt-1 text-xs text-brand-neutral">
                                {label}
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            <Card className="hidden lg:block">
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-brand-primary/3">
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        ID
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        Nume
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        Email
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        Subiect
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        Mesaj
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        Limbă
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        Creat
                                    </th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold tracking-wide text-brand-primary uppercase">
                                        Acțiuni
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {inquiries.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={8}
                                            className="px-4 py-8 text-center text-brand-neutral"
                                        >
                                            Nu există mesaje momentan.
                                        </td>
                                    </tr>
                                )}

                                {inquiries.data.map((inquiry) => (
                                    <tr
                                        key={inquiry.id}
                                        className="border-b border-border/50 align-top transition-colors hover:bg-brand-primary/2"
                                    >
                                        <td className="px-4 py-3 text-xs font-semibold text-brand-primary">
                                            {inquiry.id}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-primary/80">
                                            {inquiry.name}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-primary/80">
                                            {inquiry.email}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-primary/80">
                                            {inquiry.subject}
                                        </td>
                                        <td
                                            className="px-4 py-3 text-xs leading-5 text-brand-primary/70"
                                            title={inquiry.message}
                                        >
                                            {truncate(inquiry.message)}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-neutral uppercase">
                                            {inquiry.locale}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-brand-neutral">
                                            {new Date(
                                                inquiry.created_at,
                                            ).toLocaleString()}
                                        </td>
                                        <td className="px-4 py-3 text-xs">
                                            <a
                                                href={`mailto:${inquiry.email}?subject=${encodeURIComponent(`Re: ${inquiry.subject}`)}`}
                                                className="font-medium text-brand-secondary transition-colors hover:text-brand-primary"
                                            >
                                                Răspunde
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {inquiries.links && inquiries.links.length > 3 && (
                        <div className="flex items-center justify-center gap-1 border-t p-4">
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
                                    className={`cursor-pointer rounded px-3 py-1 text-sm ${
                                        link.active
                                            ? "bg-brand-primary text-white"
                                            : "text-gray-600 hover:bg-gray-100"
                                    } ${!link.url ? "cursor-not-allowed opacity-50" : ""}`}
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
