import { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Modal from "@/Components/Modal";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Textarea } from "@/Components/ui/textarea";
import { Checkbox } from "@/Components/ui/checkbox";
import { Label } from "@/Components/ui/label";
import { Settings as SettingsType } from "@/types";

interface SettingsForm {
    rental_living_ro: string;
    rental_living_eng: string;
    buying_living_ro: string;
    buying_living_eng: string;
    auto_send: boolean;
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

interface ExchangeRateResponse {
    rate: string;
    date?: string;
    provider?: string;
    message?: string;
}

export default function Settings({ settings }: { settings: SettingsType }) {
    const { errors } = usePage().props;
    const [form, setForm] = useState<SettingsForm>({
        rental_living_ro: settings?.rental_living_ro || "",
        rental_living_eng: settings?.rental_living_eng || "",
        buying_living_ro: settings?.buying_living_ro || "",
        buying_living_eng: settings?.buying_living_eng || "",
        auto_send: settings?.auto_send || false,
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
    const [generatingPdf, setGeneratingPdf] = useState(false);
    const [isPromptEditorOpen, setIsPromptEditorOpen] = useState(false);
    const [exchangeRateMessage, setExchangeRateMessage] = useState<
        string | null
    >(null);
    const [exchangeRateError, setExchangeRateError] = useState<string | null>(
        null,
    );
    const [selectedPromptKey, setSelectedPromptKey] =
        useState<keyof SettingsForm>("rental_living_ro");
    const [selectedPreviewKey, setSelectedPreviewKey] =
        useState("rental_living_ro");

    const activePrompt = reportOptions.find(
        (option) => option.value === selectedPromptKey,
    );
    const activePreview = reportOptions.find(
        (option) => option.value === selectedPreviewKey,
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

            <h1 className="text-2xl font-bold text-brand-primary mb-6">
                Setări
            </h1>

            <form onSubmit={handleSubmit}>
                <Card className="mb-6">
                    <CardHeader>
                        <CardTitle className="text-brand-primary">
                            Prompturi de generare
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="prompt-config">
                                Configurație raport
                            </Label>
                            <select
                                id="prompt-config"
                                value={selectedPromptKey}
                                onChange={(e) =>
                                    setSelectedPromptKey(
                                        e.target.value as keyof SettingsForm,
                                    )
                                }
                                className="flex h-11 w-full border border-brand-primary/15 bg-white px-3 text-sm text-brand-primary outline-none transition-colors focus:border-brand-secondary"
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
                        </div>

                        <div className="space-y-2">
                            <div className="flex items-center justify-between gap-4">
                                <Label htmlFor="active-prompt">
                                    Prompt activ
                                </Label>
                                <span className="text-xs text-brand-primary/60">
                                    {activePrompt?.json}
                                </span>
                            </div>

                            <button
                                id="active-prompt"
                                type="button"
                                onClick={openPromptEditor}
                                className="w-full rounded-2xl border border-brand-primary/15 bg-white px-4 py-4 text-left transition-colors hover:border-brand-secondary/40 hover:bg-brand-primary/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-secondary/25"
                            >
                                <div className="flex items-start justify-between gap-4">
                                    <div className="min-w-0">
                                        <p className="text-sm font-semibold text-brand-primary">
                                            {activePrompt?.label}
                                        </p>
                                        <p className="mt-1 text-xs text-brand-primary/60">
                                            Apasă pentru a edita promptul
                                            într-un modal dedicat.
                                        </p>
                                    </div>

                                    <span className="shrink-0 rounded-full bg-brand-primary/5 px-3 py-1 text-xs font-medium text-brand-primary">
                                        Editează
                                    </span>
                                </div>

                                <p className="mt-4 text-sm leading-6 text-brand-primary/75">
                                    {promptPreview
                                        ? `${promptPreview.slice(0, 260)}${promptPreview.length > 260 ? "..." : ""}`
                                        : "Promptul este gol. Apasă aici pentru a adăuga conținut."}
                                </p>
                            </button>

                            {errors?.[selectedPromptKey] ? (
                                <p className="text-sm text-red-600">
                                    {String(errors[selectedPromptKey])}
                                </p>
                            ) : null}
                        </div>
                    </CardContent>
                </Card>

                <Card className="mb-6">
                    <CardHeader>
                        <CardTitle className="text-brand-primary">
                            Pricing și Stripe Checkout
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div className="grid gap-4 md:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="pricing_rental_living_eur">
                                    Preț bază EUR · Rental Living
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
                                    className="h-11 border-brand-primary/15 text-brand-primary"
                                />
                                <p className="text-xs text-brand-primary/60">
                                    .com:{" "}
                                    {formatMoneyPreview(
                                        Number(
                                            form.pricing_rental_living_eur || 0,
                                        ),
                                        "EUR",
                                    )}{" "}
                                    · .ro:{" "}
                                    {ronPreview(form.pricing_rental_living_eur)}
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
                                <Label htmlFor="pricing_buying_living_eur">
                                    Preț bază EUR · Buying Living
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
                                    className="h-11 border-brand-primary/15 text-brand-primary"
                                />
                                <p className="text-xs text-brand-primary/60">
                                    .com:{" "}
                                    {formatMoneyPreview(
                                        Number(
                                            form.pricing_buying_living_eur || 0,
                                        ),
                                        "EUR",
                                    )}{" "}
                                    · .ro:{" "}
                                    {ronPreview(form.pricing_buying_living_eur)}
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

                        <div className="grid gap-4 md:grid-cols-2">
                            <div className="space-y-2">
                                <div className="flex items-center justify-between gap-3">
                                    <Label htmlFor="pricing_exchange_rate_eur_ron">
                                        Curs EUR → RON folosit în checkout
                                    </Label>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        disabled={fetchingRate}
                                        onClick={fetchExchangeRate}
                                        className="border-brand-primary/15 text-brand-primary hover:bg-brand-primary/5"
                                    >
                                        {fetchingRate
                                            ? "Se preia cursul..."
                                            : "Preia automat cursul"}
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
                                    className="h-11 border-brand-primary/15 text-brand-primary"
                                />
                                <p className="text-xs text-brand-primary/60">
                                    Checkout-ul de pe domeniul .ro convertește
                                    automat prețul din EUR folosind acest curs,
                                    apoi îl rotunjește înainte de trimiterea
                                    către Stripe.
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

                            <div className="rounded-lg border border-brand-primary/10 bg-brand-primary/3 px-4 py-4 text-sm text-brand-primary/72">
                                <p className="font-semibold text-brand-primary">
                                    Regula activă
                                </p>
                                <p className="mt-2">
                                    Domeniul .com folosește direct prețul de
                                    bază în EUR.
                                </p>
                                <p className="mt-1">
                                    Domeniul .ro calculează suma finală în RON
                                    din prețul EUR și cursul setat aici.
                                </p>
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="stripe_product_rental_living">
                                    Stripe Product ID · Rental Living
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
                                    className="h-11 border-brand-primary/15 text-brand-primary"
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
                                <Label htmlFor="stripe_product_buying_living">
                                    Stripe Product ID · Buying Living
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
                                    className="h-11 border-brand-primary/15 text-brand-primary"
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

                <Card className="mb-6">
                    <CardContent>
                        <div className="flex items-center gap-3">
                            <Checkbox
                                id="auto_send"
                                checked={form.auto_send}
                                onCheckedChange={(checked) =>
                                    setForm({ ...form, auto_send: !!checked })
                                }
                            />
                            <Label
                                htmlFor="auto_send"
                                className="cursor-pointer"
                            >
                                Trimite automat rapoartele după generare
                            </Label>
                        </div>
                    </CardContent>
                </Card>

                <Button
                    type="submit"
                    disabled={processing}
                    className="bg-brand-primary hover:bg-brand-primary/90 text-white cursor-pointer"
                >
                    {processing ? "Se salvează…" : "Salvează Setările"}
                </Button>
            </form>

            <Card className="mt-8">
                <CardHeader>
                    <CardTitle className="text-brand-primary">
                        Test PDF Template
                    </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <p className="text-sm text-muted-foreground">
                        Generează un PDF de test din fișierul JSON selectat,
                        fără apel OpenAI. Folosește aceeași structură ca în
                        fluxul real de generare.
                    </p>

                    <div className="space-y-2">
                        <Label htmlFor="preview-config">
                            Configurație preview
                        </Label>
                        <select
                            id="preview-config"
                            value={selectedPreviewKey}
                            onChange={(e) =>
                                setSelectedPreviewKey(e.target.value)
                            }
                            className="flex h-11 w-full border border-brand-primary/15 bg-white px-3 text-sm text-brand-primary outline-none transition-colors focus:border-brand-secondary"
                        >
                            {reportOptions.map((option) => (
                                <option key={option.value} value={option.value}>
                                    {option.label}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div className="flex items-center justify-between gap-4 rounded-lg border border-brand-primary/10 bg-brand-primary/3 px-4 py-3">
                        <div>
                            <p className="text-sm font-semibold text-brand-primary">
                                {activePreview?.label}
                            </p>
                            <p className="text-xs text-brand-primary/65">
                                Fișier mock: {activePreview?.json}
                            </p>
                        </div>

                        <Button
                            type="button"
                            disabled={generatingPdf}
                            onClick={() => {
                                setGeneratingPdf(true);
                                window.location.href = `/admin/test-pdf?type=${selectedPreviewKey}`;
                                setTimeout(() => setGeneratingPdf(false), 5000);
                            }}
                            className="bg-brand-primary hover:bg-brand-primary/90 text-white cursor-pointer"
                        >
                            {generatingPdf ? "Se generează…" : "Generează PDF"}
                        </Button>
                    </div>
                </CardContent>
            </Card>

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
                            <p className="mt-1 text-xs text-brand-primary/55">
                                Fișier mock: {activePrompt?.json}
                            </p>
                        </div>

                        <Button
                            type="button"
                            variant="outline"
                            onClick={closePromptEditor}
                            className="border-brand-primary/15 text-brand-primary hover:bg-brand-primary/5"
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
                            className="min-h-96 border-brand-primary/15 text-sm leading-6 text-brand-primary"
                        />

                        <div className="flex items-center justify-between gap-4 text-xs text-brand-primary/60">
                            <span>
                                Modificările rămân locale până apeși "Salvează
                                Setările".
                            </span>
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
                            className="bg-brand-primary hover:bg-brand-primary/90 text-white"
                        >
                            Gata
                        </Button>
                    </div>
                </div>
            </Modal>
        </AdminLayout>
    );
}
