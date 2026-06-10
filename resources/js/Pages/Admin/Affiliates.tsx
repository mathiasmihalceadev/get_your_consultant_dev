import { FormEvent, useState } from "react";
import { Head, router, useForm } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { buttonVariants } from "@/Components/ui/button";
import { AffiliateTag, PaginatedData } from "@/types";
import { cn } from "@/lib/utils";

const sectionClass =
    "rounded-none border border-brand-primary/10 bg-white shadow-none";
const sectionHeaderClass =
    "rounded-none border-b border-brand-primary/10 px-5 py-4";
const surfaceClass =
    "border border-brand-primary/10 bg-brand-primary/[0.02] px-4 py-4";

interface AffiliateCounts {
    total: number;
    active: number;
    reports: number;
    paid_purchases: number;
}

interface AffiliateTracking {
    parameter: string;
    cookie_days: number;
    base_urls: Record<string, string>;
}

interface AffiliatesProps {
    tags: PaginatedData<AffiliateTag>;
    counts: AffiliateCounts;
    tracking: AffiliateTracking;
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

function formatRevenue(tag: AffiliateTag): string {
    if (tag.revenue_totals.length === 0) {
        return "-";
    }

    return tag.revenue_totals
        .map((total) => {
            try {
                return new Intl.NumberFormat("ro-RO", {
                    style: "currency",
                    currency: total.currency,
                }).format(total.amount_minor / 100);
            } catch {
                return `${(total.amount_minor / 100).toFixed(2)} ${total.currency}`;
            }
        })
        .join(" / ");
}

function trackingUrls(tag: AffiliateTag, tracking: AffiliateTracking): string[] {
    return Object.values(tracking.base_urls).map(
        (baseUrl) => `${baseUrl}/?${tracking.parameter}=${tag.slug}`,
    );
}

function TagEditForm({
    tag,
    tracking,
}: {
    tag: AffiliateTag;
    tracking: AffiliateTracking;
}) {
    const [copied, setCopied] = useState<string | null>(null);
    const form = useForm({
        name: tag.name,
        slug: tag.slug,
        notes: tag.notes ?? "",
        is_active: tag.is_active,
    });
    const urls = trackingUrls(tag, tracking);

    function submit(event: FormEvent) {
        event.preventDefault();

        form.patch(`/admin/affiliates/${tag.id}`, {
            preserveScroll: true,
        });
    }

    function copyUrl(url: string) {
        void navigator.clipboard?.writeText(url);
        setCopied(url);
        window.setTimeout(() => setCopied(null), 1600);
    }

    return (
        <form
            onSubmit={submit}
            className="space-y-4 border-b border-brand-primary/10 px-5 py-5 last:border-b-0"
        >
            <div className="grid gap-4 lg:grid-cols-[1fr_1fr_auto] lg:items-end">
                <div className="space-y-2">
                    <Label htmlFor={`name-${tag.id}`}>Nume influencer</Label>
                    <Input
                        id={`name-${tag.id}`}
                        value={form.data.name}
                        onChange={(event) =>
                            form.setData("name", event.currentTarget.value)
                        }
                        className="h-10 rounded-none border-brand-primary/15"
                    />
                    {form.errors.name && (
                        <p className="text-xs text-red-600">
                            {form.errors.name}
                        </p>
                    )}
                </div>

                <div className="space-y-2">
                    <Label htmlFor={`slug-${tag.id}`}>Tag URL</Label>
                    <Input
                        id={`slug-${tag.id}`}
                        value={form.data.slug}
                        onChange={(event) =>
                            form.setData("slug", event.currentTarget.value)
                        }
                        className="h-10 rounded-none border-brand-primary/15 font-mono"
                    />
                    {form.errors.slug && (
                        <p className="text-xs text-red-600">
                            {form.errors.slug}
                        </p>
                    )}
                </div>

                <label className="flex h-10 items-center gap-2 border border-brand-primary/10 px-3 text-sm text-brand-primary">
                    <input
                        type="checkbox"
                        checked={form.data.is_active}
                        onChange={(event) =>
                            form.setData(
                                "is_active",
                                event.currentTarget.checked,
                            )
                        }
                    />
                    Activ
                </label>
            </div>

            <div className="space-y-2">
                <Label htmlFor={`notes-${tag.id}`}>Note interne</Label>
                <textarea
                    id={`notes-${tag.id}`}
                    value={form.data.notes}
                    onChange={(event) =>
                        form.setData("notes", event.currentTarget.value)
                    }
                    rows={2}
                    className="w-full rounded-none border border-brand-primary/15 px-3 py-2 text-sm text-brand-primary outline-none focus:border-brand-primary/40"
                />
                {form.errors.notes && (
                    <p className="text-xs text-red-600">{form.errors.notes}</p>
                )}
            </div>

            <div className="grid gap-3 md:grid-cols-4">
                <div className={surfaceClass}>
                    <p className="text-xs text-brand-primary/55">
                        Rapoarte pornite
                    </p>
                    <p className="mt-1 text-lg font-semibold text-brand-primary">
                        {tag.reports_count}
                    </p>
                </div>
                <div className={surfaceClass}>
                    <p className="text-xs text-brand-primary/55">
                        Checkout-uri
                    </p>
                    <p className="mt-1 text-lg font-semibold text-brand-primary">
                        {tag.purchases_count}
                    </p>
                </div>
                <div className={surfaceClass}>
                    <p className="text-xs text-brand-primary/55">
                        Plati confirmate
                    </p>
                    <p className="mt-1 text-lg font-semibold text-brand-primary">
                        {tag.paid_purchases_count}
                    </p>
                </div>
                <div className={surfaceClass}>
                    <p className="text-xs text-brand-primary/55">Venit platit</p>
                    <p className="mt-1 text-lg font-semibold text-brand-primary">
                        {formatRevenue(tag)}
                    </p>
                </div>
            </div>

            <div className="space-y-2">
                <p className="text-sm font-medium text-brand-primary">
                    Link-uri de tracking
                </p>
                {urls.map((url) => (
                    <div
                        key={url}
                        className="flex flex-col gap-2 border border-brand-primary/10 bg-white px-3 py-3 text-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <code className="break-all text-brand-primary/75">
                            {url}
                        </code>
                        <button
                            type="button"
                            onClick={() => copyUrl(url)}
                            className={cn(
                                buttonVariants({
                                    variant: "outline",
                                    size: "sm",
                                }),
                                "h-8 rounded-none border-brand-primary/15 text-brand-primary hover:bg-brand-primary/4",
                            )}
                        >
                            {copied === url ? "Copiat" : "Copiaza"}
                        </button>
                    </div>
                ))}
            </div>

            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p className="text-xs text-brand-primary/55">
                    Creat: {formatDateTime(tag.created_at)} | Ultima folosire:{" "}
                    {formatDateTime(tag.last_used_at)}
                </p>

                <button
                    type="submit"
                    disabled={form.processing}
                    className={cn(
                        buttonVariants({ size: "lg" }),
                        "h-10 rounded-none bg-brand-primary text-white hover:bg-brand-primary/92",
                    )}
                >
                    Salveaza
                </button>
            </div>
        </form>
    );
}

export default function Affiliates({
    tags,
    counts,
    tracking,
}: AffiliatesProps) {
    const createForm = useForm({
        name: "",
        slug: "",
        notes: "",
        is_active: true,
    });

    function createTag(event: FormEvent) {
        event.preventDefault();

        createForm.post("/admin/affiliates", {
            preserveScroll: true,
            onSuccess: () => createForm.reset(),
        });
    }

    const summaryCards = [
        { label: "Total tag-uri", value: counts.total },
        { label: "Active", value: counts.active },
        { label: "Rapoarte atribuite", value: counts.reports },
        { label: "Plati confirmate", value: counts.paid_purchases },
    ];

    return (
        <AdminLayout>
            <Head title="Afiliati" />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 border-b border-brand-primary/10 pb-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-brand-primary">
                            Afiliati
                        </h1>
                        <p className="mt-1 text-sm text-brand-primary/60">
                            Creeaza tag-uri pentru influenceri si urmareste
                            rapoartele si platile atribuite.
                        </p>
                    </div>

                    <p className="text-sm text-brand-primary/60">
                        Cookie ref: {tracking.cookie_days} zile
                    </p>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {summaryCards.map((card) => (
                        <Card key={card.label} className={sectionClass}>
                            <CardContent className="space-y-2 px-5 py-5">
                                <p className="text-sm text-brand-primary/60">
                                    {card.label}
                                </p>
                                <p className="text-3xl font-semibold text-brand-primary">
                                    {card.value}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <CardTitle className="text-base font-semibold text-brand-primary">
                            Tag nou
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="px-5 py-5">
                        <form onSubmit={createTag} className="space-y-4">
                            <div className="grid gap-4 lg:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="new-name">
                                        Nume influencer
                                    </Label>
                                    <Input
                                        id="new-name"
                                        value={createForm.data.name}
                                        onChange={(event) =>
                                            createForm.setData(
                                                "name",
                                                event.currentTarget.value,
                                            )
                                        }
                                        className="h-10 rounded-none border-brand-primary/15"
                                        placeholder="Ex: Maria Popescu"
                                    />
                                    {createForm.errors.name && (
                                        <p className="text-xs text-red-600">
                                            {createForm.errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="new-slug">
                                        Tag URL optional
                                    </Label>
                                    <Input
                                        id="new-slug"
                                        value={createForm.data.slug}
                                        onChange={(event) =>
                                            createForm.setData(
                                                "slug",
                                                event.currentTarget.value,
                                            )
                                        }
                                        className="h-10 rounded-none border-brand-primary/15 font-mono"
                                        placeholder="maria-popescu"
                                    />
                                    {createForm.errors.slug && (
                                        <p className="text-xs text-red-600">
                                            {createForm.errors.slug}
                                        </p>
                                    )}
                                </div>

                                <label className="flex h-10 items-center gap-2 self-end border border-brand-primary/10 px-3 text-sm text-brand-primary">
                                    <input
                                        type="checkbox"
                                        checked={createForm.data.is_active}
                                        onChange={(event) =>
                                            createForm.setData(
                                                "is_active",
                                                event.currentTarget.checked,
                                            )
                                        }
                                    />
                                    Activ
                                </label>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="new-notes">Note interne</Label>
                                <textarea
                                    id="new-notes"
                                    value={createForm.data.notes}
                                    onChange={(event) =>
                                        createForm.setData(
                                            "notes",
                                            event.currentTarget.value,
                                        )
                                    }
                                    rows={2}
                                    className="w-full rounded-none border border-brand-primary/15 px-3 py-2 text-sm text-brand-primary outline-none focus:border-brand-primary/40"
                                />
                                {createForm.errors.notes && (
                                    <p className="text-xs text-red-600">
                                        {createForm.errors.notes}
                                    </p>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={createForm.processing}
                                className={cn(
                                    buttonVariants({ size: "lg" }),
                                    "h-10 rounded-none bg-brand-primary text-white hover:bg-brand-primary/92",
                                )}
                            >
                                Creeaza tag
                            </button>
                        </form>
                    </CardContent>
                </Card>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <CardTitle className="text-base font-semibold text-brand-primary">
                                Tag-uri existente
                            </CardTitle>
                            <p className="text-sm text-brand-primary/60">
                                Afisate acum: {tags.data.length}
                            </p>
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        {tags.data.length === 0 ? (
                            <div className="px-5 py-10 text-center text-sm text-brand-primary/60">
                                Nu exista tag-uri de afiliat momentan.
                            </div>
                        ) : (
                            tags.data.map((tag) => (
                                <TagEditForm
                                    key={tag.id}
                                    tag={tag}
                                    tracking={tracking}
                                />
                            ))
                        )}

                        {tags.links && tags.links.length > 3 && (
                            <div className="flex flex-wrap items-center justify-center gap-2 border-t border-brand-primary/10 px-5 py-4">
                                {tags.links.map((link, index) => (
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
