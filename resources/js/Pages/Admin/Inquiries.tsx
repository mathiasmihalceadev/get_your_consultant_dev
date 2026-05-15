import { Head, router } from "@inertiajs/react";
import { Card, CardContent } from "@/Components/ui/card";
import {
    ClockCountdown,
    EnvelopeSimple,
    ListDashes,
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

export default function Inquiries({ inquiries, counts }: InquiriesProps) {
    return (
        <AdminLayout>
            <Head title="Mesaje" />

            <h1 className="mb-6 text-2xl font-bold text-brand-primary">
                Mesaje primite
            </h1>

            <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                {[
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
                            <div className="mt-1 text-xs text-brand-neutral">
                                {label}
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            <Card>
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
