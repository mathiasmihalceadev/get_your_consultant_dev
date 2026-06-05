import { useState } from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Checkbox } from "@/Components/ui/checkbox";
import { Label } from "@/Components/ui/label";
import { Report, ReportStatus, ReportType } from "@/types";
import { CaretDown } from "@phosphor-icons/react";

type PreviewKey =
    | "rental_living_ro"
    | "rental_living_eng"
    | "buying_living_ro"
    | "buying_living_eng";

const reportOptions = [
    {
        value: "rental_living_ro",
        label: "Închiriere rezidențială · RO",
        json: "rental_ro.json",
    },
    {
        value: "rental_living_eng",
        label: "Rental residential · EN",
        json: "rental_eng.json",
    },
    {
        value: "buying_living_ro",
        label: "Cumpărare rezidențială · RO",
        json: "buying_ro.json",
    },
    {
        value: "buying_living_eng",
        label: "Buying residential · EN",
        json: "buying_eng.json",
    },
] as const;

const billingStatusConfig: Record<
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

const billingTypeLabels: Record<ReportType, string> = {
    rental_living: "Închiriere rezidențială",
    rental_business: "Închiriere business",
    buying_living: "Cumpărare rezidențială",
    buying_business: "Cumpărare business",
};

const sectionClass =
    "rounded-none border border-brand-primary/10 bg-white shadow-none";
const sectionHeaderClass =
    "rounded-none border-b border-brand-primary/10 px-5 py-4";
const sectionBodyClass = "space-y-5 px-5 py-5";
const inputClass =
    "h-11 rounded-none border-brand-primary/15 text-brand-primary shadow-none";
const selectClass =
    "h-11 w-full appearance-none rounded-none border border-brand-primary/15 bg-white px-3 pr-10 text-sm text-brand-primary outline-none transition-colors focus:border-brand-secondary";
const surfaceClass =
    "border border-brand-primary/10 bg-brand-primary/[0.02] p-4";

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

export default function TestTools({
    billingTests,
    billingTestCompletedCount,
}: {
    billingTests: Report[];
    billingTestCompletedCount: number;
}) {
    const billingTestForm = useForm({
        email: "",
        locale: "ro",
        report_type: "buying_living" as ReportType,
        send_test_email: false,
    });
    const [selectedPreviewKey, setSelectedPreviewKey] =
        useState<PreviewKey>("rental_living_ro");

    const activePreview = reportOptions.find(
        (option) => option.value === selectedPreviewKey,
    );
    const currentBillingTestAmount =
        billingTestForm.data.locale === "ro" ? "5.00 RON" : "1.00 EUR";

    const handleBillingTestCheckout = () => {
        billingTestForm.post("/admin/billing-tests/checkout");
    };

    return (
        <AdminLayout>
            <Head title="Teste" />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 border-b border-brand-primary/10 pb-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-brand-primary">
                            Teste interne
                        </h1>
                        <p className="mt-1 text-sm text-brand-primary/60">
                            Checkout de test și generare PDF.
                        </p>
                    </div>

                    <Link
                        href="/admin/settings"
                        className="inline-flex h-11 items-center justify-center border border-brand-primary/15 px-4 text-sm font-medium text-brand-primary transition-colors hover:bg-brand-primary/4"
                    >
                        Înapoi la setări
                    </Link>
                </div>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <CardTitle className="text-base font-semibold text-brand-primary">
                                Checkout de test
                            </CardTitle>
                            <div className="border border-brand-primary/10 px-3 py-2 text-sm font-medium text-brand-primary">
                                Sumă: {currentBillingTestAmount}
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className={sectionBodyClass}>
                        <div className="border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            SmartBill emite documente reale dacă folosești
                            credențiale live.
                        </div>

                        <div className="grid gap-5 md:grid-cols-2">
                            <div className="space-y-2 md:col-span-2">
                                <Label
                                    htmlFor="billing-test-email"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Email
                                </Label>
                                <Input
                                    id="billing-test-email"
                                    type="email"
                                    value={billingTestForm.data.email}
                                    onChange={(event) =>
                                        billingTestForm.setData(
                                            "email",
                                            event.target.value,
                                        )
                                    }
                                    placeholder="billing-test@example.com"
                                    required
                                    className={inputClass}
                                />
                                {billingTestForm.errors.email ? (
                                    <p className="text-sm text-red-600">
                                        {billingTestForm.errors.email}
                                    </p>
                                ) : null}
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="billing-test-locale"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Limbă și monedă
                                </Label>
                                <div className="relative">
                                    <select
                                        id="billing-test-locale"
                                        value={billingTestForm.data.locale}
                                        onChange={(event) =>
                                            billingTestForm.setData(
                                                "locale",
                                                event.target.value,
                                            )
                                        }
                                        className={selectClass}
                                    >
                                        <option value="ro">
                                            Română / RON / 5.00
                                        </option>
                                        <option value="en">
                                            English / EUR / 1.00
                                        </option>
                                    </select>
                                    <CaretDown
                                        size={16}
                                        className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-brand-primary/48"
                                    />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="billing-test-type"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Tip raport
                                </Label>
                                <div className="relative">
                                    <select
                                        id="billing-test-type"
                                        value={billingTestForm.data.report_type}
                                        onChange={(event) =>
                                            billingTestForm.setData(
                                                "report_type",
                                                event.target
                                                    .value as ReportType,
                                            )
                                        }
                                        className={selectClass}
                                    >
                                        <option value="buying_living">
                                            Cumpărare rezidențială
                                        </option>
                                        <option value="rental_living">
                                            Închiriere rezidențială
                                        </option>
                                    </select>
                                    <CaretDown
                                        size={16}
                                        className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-brand-primary/48"
                                    />
                                </div>
                            </div>
                        </div>

                        <div className={surfaceClass}>
                            <div className="flex items-start gap-3">
                                <Checkbox
                                    id="send_test_email"
                                    className="mt-1 rounded-none border-brand-primary/20"
                                    checked={
                                        billingTestForm.data.send_test_email
                                    }
                                    onCheckedChange={(checked) =>
                                        billingTestForm.setData(
                                            "send_test_email",
                                            !!checked,
                                        )
                                    }
                                />
                                <div>
                                    <Label
                                        htmlFor="send_test_email"
                                        className="cursor-pointer text-sm font-medium text-brand-primary"
                                    >
                                        Trimite emailul de test cu factura
                                    </Label>
                                    <p className="mt-1 text-sm text-brand-primary/66">
                                        Nu atașează PDF-ul raportului.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p className="text-sm text-brand-primary/60">
                                După checkout ajungi direct în raportul de test
                                din admin.
                            </p>
                            <Button
                                type="button"
                                onClick={handleBillingTestCheckout}
                                disabled={billingTestForm.processing}
                                className="h-11 rounded-none bg-brand-primary px-5 text-white hover:bg-brand-primary/92"
                            >
                                {billingTestForm.processing
                                    ? "Se pregătește..."
                                    : "Pornește checkout-ul de test"}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <CardTitle className="text-base font-semibold text-brand-primary">
                                Ultimele teste
                            </CardTitle>
                            <div className="border border-brand-primary/10 px-3 py-2 text-sm font-medium text-brand-primary">
                                {billingTestCompletedCount} finalizate
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className={sectionBodyClass}>
                        {billingTests.length === 0 ? (
                            <p className="text-sm text-brand-primary/60">
                                Nu există teste pornite încă.
                            </p>
                        ) : (
                            <div className="border border-brand-primary/10">
                                {billingTests.map((report) => (
                                    <Link
                                        key={report.id}
                                        href={`/admin/reports/${report.id}`}
                                        className="block border-b border-brand-primary/10 px-4 py-4 transition-colors last:border-b-0 hover:bg-brand-primary/3"
                                    >
                                        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                            <div className="min-w-0">
                                                <p className="text-sm font-medium text-brand-primary">
                                                    Test #{report.id}
                                                </p>
                                                <p className="mt-1 text-sm text-brand-primary/72">
                                                    {billingTypeLabels[
                                                        report.report_type
                                                    ] ?? report.report_type}
                                                </p>
                                                <p className="mt-2 text-xs text-brand-primary/60">
                                                    {report.email ||
                                                        "Fără email"}
                                                    {" · "}
                                                    {formatDateTime(
                                                        report.created_at,
                                                    )}
                                                </p>
                                            </div>

                                            <span
                                                className={`inline-flex items-center px-2.5 py-1 text-xs font-medium ${billingStatusConfig[report.status].bg} ${billingStatusConfig[report.status].text}`}
                                            >
                                                {
                                                    billingStatusConfig[
                                                        report.status
                                                    ].label
                                                }
                                            </span>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <CardTitle className="text-base font-semibold text-brand-primary">
                            Test PDF
                        </CardTitle>
                    </CardHeader>
                    <CardContent className={sectionBodyClass}>
                        <div className="grid gap-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                            <div className="space-y-2">
                                <Label
                                    htmlFor="preview-config"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Configurație
                                </Label>
                                <div className="relative">
                                    <select
                                        id="preview-config"
                                        value={selectedPreviewKey}
                                        onChange={(e) =>
                                            setSelectedPreviewKey(
                                                e.target.value as PreviewKey,
                                            )
                                        }
                                        className={selectClass}
                                    >
                                        {reportOptions.map((option) => (
                                            <option
                                                key={option.value}
                                                value={option.value}
                                            >
                                                {option.label}
                                            </option>
                                        ))}
                                    </select>
                                    <CaretDown
                                        size={16}
                                        className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-brand-primary/48"
                                    />
                                </div>
                            </div>

                            <a
                                href={`/admin/test-pdf?type=${selectedPreviewKey}`}
                                className="inline-flex h-11 items-center justify-center bg-brand-primary px-5 text-sm font-medium text-white transition-colors hover:bg-brand-primary/92"
                            >
                                Generează PDF
                            </a>
                        </div>

                        <div className={surfaceClass}>
                            <p className="text-sm font-medium text-brand-primary">
                                {activePreview?.label}
                            </p>
                            <p className="mt-1 text-sm text-brand-primary/66">
                                Fișier mock: {activePreview?.json}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
