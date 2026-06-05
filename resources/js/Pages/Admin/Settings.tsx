import { useState } from "react";
import { Head, Link, router, usePage } from "@inertiajs/react";
import Modal from "@/Components/Modal";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Textarea } from "@/Components/ui/textarea";
import { Checkbox } from "@/Components/ui/checkbox";
import { Label } from "@/Components/ui/label";
import { Settings as SettingsType } from "@/types";
import { CaretDown } from "@phosphor-icons/react";

type PromptSettingKey =
    | "rental_living_ro"
    | "rental_living_eng"
    | "buying_living_ro"
    | "buying_living_eng";

interface SettingsForm {
    rental_living_ro: string;
    rental_living_eng: string;
    buying_living_ro: string;
    buying_living_eng: string;
    auto_send: boolean;
    report_ready_notification_emails: string;
    pricing_rental_living_eur: string;
    pricing_buying_living_eur: string;
    pricing_exchange_rate_eur_ron: string;
    stripe_product_rental_living: string;
    stripe_product_buying_living: string;
    [key: string]: string | boolean;
}

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

const sectionClass =
    "rounded-none border border-brand-primary/10 bg-white shadow-none";
const sectionHeaderClass =
    "rounded-none border-b border-brand-primary/10 px-5 py-4";
const sectionBodyClass = "space-y-5 px-5 py-5";
const inputClass =
    "h-11 rounded-none border-brand-primary/15 text-brand-primary shadow-none";
const textareaClass =
    "rounded-none border-brand-primary/15 text-brand-primary shadow-none";
const selectClass =
    "h-11 w-full appearance-none rounded-none border border-brand-primary/15 bg-white px-3 pr-10 text-sm text-brand-primary outline-none transition-colors focus:border-brand-secondary";
const helperClass = "text-xs leading-5 text-brand-primary/60";
const surfaceClass =
    "border border-brand-primary/10 bg-brand-primary/[0.02] p-4";

interface ExchangeRateResponse {
    rate: string;
    date?: string;
    provider?: string;
    message?: string;
}

export default function Settings({ settings }: { settings: SettingsType }) {
    const { errors } = usePage().props as {
        errors?: Record<string, string>;
    };
    const [form, setForm] = useState<SettingsForm>({
        rental_living_ro: settings?.rental_living_ro || "",
        rental_living_eng: settings?.rental_living_eng || "",
        buying_living_ro: settings?.buying_living_ro || "",
        buying_living_eng: settings?.buying_living_eng || "",
        auto_send: settings?.auto_send || false,
        report_ready_notification_emails:
            settings?.report_ready_notification_emails || "",
        pricing_rental_living_eur:
            settings?.pricing_rental_living_eur || "17.99",
        pricing_buying_living_eur:
            settings?.pricing_buying_living_eur || "27.99",
        pricing_exchange_rate_eur_ron:
            settings?.pricing_exchange_rate_eur_ron || "5.00",
        stripe_product_rental_living:
            settings?.stripe_product_rental_living || "",
        stripe_product_buying_living:
            settings?.stripe_product_buying_living || "",
    });
    const [processing, setProcessing] = useState(false);
    const [fetchingRate, setFetchingRate] = useState(false);
    const [isPromptEditorOpen, setIsPromptEditorOpen] = useState(false);
    const [exchangeRateMessage, setExchangeRateMessage] = useState<
        string | null
    >(null);
    const [exchangeRateError, setExchangeRateError] = useState<string | null>(
        null,
    );
    const [selectedPromptKey, setSelectedPromptKey] =
        useState<PromptSettingKey>("rental_living_ro");

    const activePrompt = reportOptions.find(
        (option) => option.value === selectedPromptKey,
    );
    const activePromptValue = String(form[selectedPromptKey] || "");
    const promptPreview = activePromptValue.replace(/\s+/g, " ").trim();

    const formatMoneyPreview = (amount: number, currency: string) => {
        return new Intl.NumberFormat(currency === "RON" ? "ro-RO" : "en-IE", {
            style: "currency",
            currency,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);
    };

    const ronPreview = (eurValue: string) => {
        const eurAmount = Number(eurValue);
        const rate = Number(form.pricing_exchange_rate_eur_ron);

        if (!Number.isFinite(eurAmount) || !Number.isFinite(rate)) {
            return "-";
        }

        return formatMoneyPreview(eurAmount * rate, "RON");
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post("/admin/settings", form, {
            onFinish: () => setProcessing(false),
        });
    };

    const closePromptEditor = () => {
        setIsPromptEditorOpen(false);
    };

    const openPromptEditor = (e: React.MouseEvent<HTMLButtonElement>) => {
        e.currentTarget.blur();
        setIsPromptEditorOpen(true);
    };

    const fetchExchangeRate = async () => {
        setFetchingRate(true);
        setExchangeRateError(null);
        setExchangeRateMessage(null);

        try {
            const response = await window.axios.get<ExchangeRateResponse>(
                "/admin/settings/exchange-rate",
            );
            const rate = String(response.data.rate || "");

            if (rate === "") {
                throw new Error("Nu am primit un curs valid de la server.");
            }

            setForm((currentForm) => ({
                ...currentForm,
                pricing_exchange_rate_eur_ron: rate,
            }));

            const provider = response.data.provider || "providerul extern";
            const dateSuffix = response.data.date
                ? ` (${response.data.date})`
                : "";

            setExchangeRateMessage(
                `Curs actualizat automat din ${provider}${dateSuffix}. Apasă \"Salvează Setările\" pentru a-l păstra.`,
            );
        } catch (error) {
            const maybeAxiosError = error as {
                response?: { data?: { message?: string } };
                message?: string;
            };

            setExchangeRateError(
                maybeAxiosError.response?.data?.message ||
                    maybeAxiosError.message ||
                    "Nu am putut prelua cursul EUR → RON acum.",
            );
        } finally {
            setFetchingRate(false);
        }
    };

    return (
        <AdminLayout>
            <Head title="Setări" />

            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="flex flex-col gap-4 border-b border-brand-primary/10 pb-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-brand-primary">
                            Setări
                        </h1>
                        <p className="mt-1 text-sm text-brand-primary/60">
                            Prompturi, prețuri, checkout și notificări.
                        </p>
                    </div>

                    <div className="flex flex-col gap-3 sm:flex-row">
                        <Link
                            href="/admin/tests"
                            className="inline-flex h-11 items-center justify-center border border-brand-primary/15 px-4 text-sm font-medium text-brand-primary transition-colors hover:bg-brand-primary/4"
                        >
                            Pagina de teste
                        </Link>
                        <Button
                            type="submit"
                            disabled={processing}
                            className="h-11 rounded-none bg-brand-primary px-5 text-white hover:bg-brand-primary/92"
                        >
                            {processing ? "Se salvează..." : "Salvează"}
                        </Button>
                    </div>
                </div>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <CardTitle className="text-base font-semibold text-brand-primary">
                            Prompturi
                        </CardTitle>
                    </CardHeader>
                    <CardContent className={sectionBodyClass}>
                        <div className="grid gap-5 lg:grid-cols-[260px_minmax(0,1fr)]">
                            <div className="space-y-2">
                                <Label
                                    htmlFor="prompt-config"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Configurație
                                </Label>
                                <div className="relative">
                                    <select
                                        id="prompt-config"
                                        value={selectedPromptKey}
                                        onChange={(e) =>
                                            setSelectedPromptKey(
                                                e.target
                                                    .value as PromptSettingKey,
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

                            <div className={surfaceClass}>
                                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div className="min-w-0">
                                        <p className="text-sm font-medium text-brand-primary">
                                            {activePrompt?.label}
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-brand-primary/75">
                                            {promptPreview
                                                ? `${promptPreview.slice(0, 320)}${promptPreview.length > 320 ? "..." : ""}`
                                                : "Promptul este gol."}
                                        </p>
                                    </div>

                                    <Button
                                        id="active-prompt"
                                        type="button"
                                        variant="outline"
                                        onClick={openPromptEditor}
                                        className="h-10 rounded-none border-brand-primary/15 px-4 text-brand-primary hover:bg-brand-primary/4"
                                    >
                                        Editează
                                    </Button>
                                </div>
                            </div>
                        </div>

                        {errors?.[selectedPromptKey] ? (
                            <p className="text-sm text-red-600">
                                {String(errors[selectedPromptKey])}
                            </p>
                        ) : null}
                    </CardContent>
                </Card>

                <div className="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
                    <Card className={sectionClass}>
                        <CardHeader className={sectionHeaderClass}>
                            <CardTitle className="text-base font-semibold text-brand-primary">
                                Prețuri și curs
                            </CardTitle>
                        </CardHeader>
                        <CardContent className={sectionBodyClass}>
                            <div className="grid gap-5 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label
                                        htmlFor="pricing_rental_living_eur"
                                        className="text-sm font-medium text-brand-primary"
                                    >
                                        Închiriere rezidențială · EUR
                                    </Label>
                                    <Input
                                        id="pricing_rental_living_eur"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.pricing_rental_living_eur}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                pricing_rental_living_eur:
                                                    e.target.value,
                                            })
                                        }
                                        className={inputClass}
                                    />
                                    <p className={helperClass}>
                                        .com:{" "}
                                        {formatMoneyPreview(
                                            Number(
                                                form.pricing_rental_living_eur ||
                                                    0,
                                            ),
                                            "EUR",
                                        )}{" "}
                                        · .ro:{" "}
                                        {ronPreview(
                                            form.pricing_rental_living_eur,
                                        )}
                                    </p>
                                    {errors?.pricing_rental_living_eur ? (
                                        <p className="text-sm text-red-600">
                                            {String(
                                                errors.pricing_rental_living_eur,
                                            )}
                                        </p>
                                    ) : null}
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor="pricing_buying_living_eur"
                                        className="text-sm font-medium text-brand-primary"
                                    >
                                        Cumpărare rezidențială · EUR
                                    </Label>
                                    <Input
                                        id="pricing_buying_living_eur"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.pricing_buying_living_eur}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                pricing_buying_living_eur:
                                                    e.target.value,
                                            })
                                        }
                                        className={inputClass}
                                    />
                                    <p className={helperClass}>
                                        .com:{" "}
                                        {formatMoneyPreview(
                                            Number(
                                                form.pricing_buying_living_eur ||
                                                    0,
                                            ),
                                            "EUR",
                                        )}{" "}
                                        · .ro:{" "}
                                        {ronPreview(
                                            form.pricing_buying_living_eur,
                                        )}
                                    </p>
                                    {errors?.pricing_buying_living_eur ? (
                                        <p className="text-sm text-red-600">
                                            {String(
                                                errors.pricing_buying_living_eur,
                                            )}
                                        </p>
                                    ) : null}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <Label
                                        htmlFor="pricing_exchange_rate_eur_ron"
                                        className="text-sm font-medium text-brand-primary"
                                    >
                                        Curs EUR → RON
                                    </Label>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        disabled={fetchingRate}
                                        onClick={fetchExchangeRate}
                                        className="h-10 rounded-none border-brand-primary/15 px-4 text-brand-primary hover:bg-brand-primary/4"
                                    >
                                        {fetchingRate
                                            ? "Se preia..."
                                            : "Preia automat"}
                                    </Button>
                                </div>
                                <Input
                                    id="pricing_exchange_rate_eur_ron"
                                    type="number"
                                    step="0.000001"
                                    min="0"
                                    value={form.pricing_exchange_rate_eur_ron}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            pricing_exchange_rate_eur_ron:
                                                e.target.value,
                                        })
                                    }
                                    className={inputClass}
                                />
                                <p className={helperClass}>
                                    Folosit doar pentru checkout-ul .ro.
                                </p>
                                {exchangeRateMessage ? (
                                    <p className="text-sm text-emerald-700">
                                        {exchangeRateMessage}
                                    </p>
                                ) : null}
                                {exchangeRateError ? (
                                    <p className="text-sm text-red-600">
                                        {exchangeRateError}
                                    </p>
                                ) : null}
                                {errors?.pricing_exchange_rate_eur_ron ? (
                                    <p className="text-sm text-red-600">
                                        {String(
                                            errors.pricing_exchange_rate_eur_ron,
                                        )}
                                    </p>
                                ) : null}
                            </div>
                        </CardContent>
                    </Card>

                    <Card className={sectionClass}>
                        <CardHeader className={sectionHeaderClass}>
                            <CardTitle className="text-base font-semibold text-brand-primary">
                                Livrare și notificări
                            </CardTitle>
                        </CardHeader>
                        <CardContent className={sectionBodyClass}>
                            <div className={surfaceClass}>
                                <div className="flex items-start gap-3">
                                    <Checkbox
                                        id="auto_send"
                                        className="mt-1 rounded-none border-brand-primary/20"
                                        checked={form.auto_send}
                                        onCheckedChange={(checked) =>
                                            setForm({
                                                ...form,
                                                auto_send: !!checked,
                                            })
                                        }
                                    />
                                    <div>
                                        <Label
                                            htmlFor="auto_send"
                                            className="cursor-pointer text-sm font-medium text-brand-primary"
                                        >
                                            Trimite automat după generare
                                        </Label>
                                        <p className="mt-1 text-sm text-brand-primary/66">
                                            Dezactivează doar dacă vrei
                                            verificare manuală înainte de email.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="report_ready_notification_emails"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Emailuri interne
                                </Label>
                                <Textarea
                                    id="report_ready_notification_emails"
                                    rows={5}
                                    value={
                                        form.report_ready_notification_emails
                                    }
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            report_ready_notification_emails:
                                                e.target.value,
                                        })
                                    }
                                    placeholder={[
                                        "ops@example.com",
                                        "team@example.com",
                                    ].join("\n")}
                                    className={textareaClass}
                                />
                                <p className={helperClass}>
                                    Separă adresele pe linii noi, prin virgulă
                                    sau prin punct și virgulă.
                                </p>
                                {errors?.report_ready_notification_emails ? (
                                    <p className="text-sm text-red-600">
                                        {String(
                                            errors.report_ready_notification_emails,
                                        )}
                                    </p>
                                ) : null}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <Card className={sectionClass}>
                    <CardHeader className={sectionHeaderClass}>
                        <CardTitle className="text-base font-semibold text-brand-primary">
                            Stripe
                        </CardTitle>
                    </CardHeader>
                    <CardContent className={sectionBodyClass}>
                        <div className="grid gap-5 md:grid-cols-2">
                            <div className="space-y-2">
                                <Label
                                    htmlFor="stripe_product_rental_living"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Product ID · Închiriere rezidențială
                                </Label>
                                <Input
                                    id="stripe_product_rental_living"
                                    value={form.stripe_product_rental_living}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            stripe_product_rental_living:
                                                e.target.value,
                                        })
                                    }
                                    placeholder="prod_..."
                                    className={inputClass}
                                />
                                {errors?.stripe_product_rental_living ? (
                                    <p className="text-sm text-red-600">
                                        {String(
                                            errors.stripe_product_rental_living,
                                        )}
                                    </p>
                                ) : null}
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor="stripe_product_buying_living"
                                    className="text-sm font-medium text-brand-primary"
                                >
                                    Product ID · Cumpărare rezidențială
                                </Label>
                                <Input
                                    id="stripe_product_buying_living"
                                    value={form.stripe_product_buying_living}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            stripe_product_buying_living:
                                                e.target.value,
                                        })
                                    }
                                    placeholder="prod_..."
                                    className={inputClass}
                                />
                                {errors?.stripe_product_buying_living ? (
                                    <p className="text-sm text-red-600">
                                        {String(
                                            errors.stripe_product_buying_living,
                                        )}
                                    </p>
                                ) : null}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end">
                    <Button
                        type="submit"
                        disabled={processing}
                        className="h-11 rounded-none bg-brand-primary px-5 text-white hover:bg-brand-primary/92"
                    >
                        {processing ? "Se salvează..." : "Salvează"}
                    </Button>
                </div>
            </form>

            <Modal
                show={isPromptEditorOpen}
                onClose={closePromptEditor}
                maxWidth="6xl"
            >
                <div className="p-6 sm:p-8">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <h2 className="text-lg font-semibold text-brand-primary">
                                Editează promptul
                            </h2>
                            <p className="mt-1 text-sm text-brand-primary/70">
                                {activePrompt?.label}
                            </p>
                        </div>

                        <Button
                            type="button"
                            variant="outline"
                            onClick={closePromptEditor}
                            className="h-10 rounded-none border-brand-primary/15 px-4 text-brand-primary hover:bg-brand-primary/4"
                        >
                            Închide
                        </Button>
                    </div>

                    <div className="mt-6 space-y-2">
                        <Label htmlFor="active-prompt-modal">
                            Conținut prompt
                        </Label>
                        <Textarea
                            id="active-prompt-modal"
                            value={activePromptValue}
                            onChange={(e) =>
                                setForm({
                                    ...form,
                                    [selectedPromptKey]: e.target.value,
                                })
                            }
                            rows={24}
                            className="min-h-96 rounded-none border-brand-primary/15 text-sm leading-6 text-brand-primary"
                        />

                        <div className="flex items-center justify-between gap-4 text-xs text-brand-primary/60">
                            <span>{activePrompt?.json}</span>
                            <span>
                                {activePromptValue.length.toLocaleString()}{" "}
                                caractere
                            </span>
                        </div>

                        {errors?.[selectedPromptKey] ? (
                            <p className="text-sm text-red-600">
                                {String(errors[selectedPromptKey])}
                            </p>
                        ) : null}
                    </div>

                    <div className="mt-6 flex justify-end">
                        <Button
                            type="button"
                            onClick={closePromptEditor}
                            className="h-10 rounded-none bg-brand-primary px-4 text-white hover:bg-brand-primary/92"
                        >
                            Gata
                        </Button>
                    </div>
                </div>
            </Modal>
        </AdminLayout>
    );
}
