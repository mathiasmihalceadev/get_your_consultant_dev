import { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Textarea } from "@/Components/ui/textarea";
import { Checkbox } from "@/Components/ui/checkbox";
import { Label } from "@/Components/ui/label";
import { Settings as SettingsType } from "@/types";

interface SettingsForm {
    rental_living_prompt: string;
    rental_living_prompt_ro: string;
    rental_business_prompt: string;
    rental_business_prompt_ro: string;
    buying_living_prompt: string;
    buying_living_prompt_ro: string;
    buying_business_prompt: string;
    buying_business_prompt_ro: string;
    auto_send: boolean;
    [key: string]: string | boolean;
}

const reportTypes = [
    { value: "rental_living", label: "Închiriere – Rezidențial" },
    { value: "rental_business", label: "Închiriere – Business" },
    { value: "buying_living", label: "Cumpărare – Rezidențial" },
    { value: "buying_business", label: "Cumpărare – Business" },
] as const;

const languages = [
    { value: "en", label: "EN", suffix: "" },
    { value: "ro", label: "RO", suffix: "_ro" },
] as const;

export default function Settings({ settings }: { settings: SettingsType }) {
    const { errors } = usePage().props;
    const [form, setForm] = useState<SettingsForm>({
        rental_living_prompt: settings?.rental_living_prompt || "",
        rental_living_prompt_ro: settings?.rental_living_prompt_ro || "",
        rental_business_prompt: settings?.rental_business_prompt || "",
        rental_business_prompt_ro: settings?.rental_business_prompt_ro || "",
        buying_living_prompt: settings?.buying_living_prompt || "",
        buying_living_prompt_ro: settings?.buying_living_prompt_ro || "",
        buying_business_prompt: settings?.buying_business_prompt || "",
        buying_business_prompt_ro: settings?.buying_business_prompt_ro || "",
        auto_send: settings?.auto_send || false,
    });
    const [processing, setProcessing] = useState(false);
    const [selectedType, setSelectedType] = useState("rental_living");
    const [selectedLang, setSelectedLang] = useState("en");

    const activeKey =
        selectedType + "_prompt" + (selectedLang === "ro" ? "_ro" : "");

    const activeLabel =
        reportTypes.find((t) => t.value === selectedType)?.label ?? "";

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post("/admin/settings", form, {
            onFinish: () => setProcessing(false),
        });
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
                        <CardTitle>Prompturi AI</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {/* Report type selector */}
                        <div className="space-y-2">
                            <Label className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
                                Tip raport
                            </Label>
                            <div className="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                {reportTypes.map((type) => (
                                    <button
                                        key={type.value}
                                        type="button"
                                        onClick={() =>
                                            setSelectedType(type.value)
                                        }
                                        className={`rounded-md px-3 py-2 text-sm font-medium transition-colors cursor-pointer ${
                                            selectedType === type.value
                                                ? "bg-brand-primary text-white"
                                                : "bg-muted text-muted-foreground hover:bg-muted/80"
                                        }`}
                                    >
                                        {type.label}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Language toggle */}
                        <div className="space-y-2">
                            <Label className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
                                Limbă
                            </Label>
                            <div className="inline-flex rounded-md overflow-hidden border">
                                {languages.map((lang) => (
                                    <button
                                        key={lang.value}
                                        type="button"
                                        onClick={() =>
                                            setSelectedLang(lang.value)
                                        }
                                        className={`px-5 py-2 text-sm font-semibold transition-colors cursor-pointer ${
                                            selectedLang === lang.value
                                                ? "bg-brand-primary text-white"
                                                : "bg-white text-muted-foreground hover:bg-muted/50"
                                        }`}
                                    >
                                        {lang.label}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Active prompt textarea */}
                        <div className="space-y-2 pt-2">
                            <Label htmlFor={activeKey}>
                                {activeLabel} ({selectedLang.toUpperCase()})
                            </Label>
                            <Textarea
                                id={activeKey}
                                value={form[activeKey] as string}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        [activeKey]: e.target.value,
                                    })
                                }
                                rows={14}
                                className="font-mono text-xs"
                            />
                            {errors?.[activeKey] && (
                                <p className="text-sm text-red-600">
                                    {
                                        (errors as Record<string, string>)[
                                            activeKey
                                        ]
                                    }
                                </p>
                            )}
                        </div>
                    </CardContent>
                </Card>

                <Card className="mb-6">
                    <CardContent className="pt-6">
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
        </AdminLayout>
    );
}
