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
    const [generatingPdf, setGeneratingPdf] = useState(false);
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
                <CardContent>
                    <p className="text-sm text-muted-foreground mb-4">
                        Generează un PDF de test folosind datele mock. Folosește
                        template-ul selectat fără a apela API-ul OpenAI.
                    </p>
                    <div className="flex items-end gap-4">
                        <Button
                            type="button"
                            disabled={generatingPdf}
                            onClick={() => {
                                setGeneratingPdf(true);
                                window.location.href = `/admin/test-pdf?type=rental`;
                                setTimeout(() => setGeneratingPdf(false), 5000);
                            }}
                            className="bg-brand-tertiary hover:bg-brand-tertiary/90 text-white cursor-pointer"
                        >
                            {generatingPdf ? "Se generează…" : "PDF Închiriere"}
                        </Button>
                        <Button
                            type="button"
                            disabled={generatingPdf}
                            onClick={() => {
                                setGeneratingPdf(true);
                                window.location.href = `/admin/test-pdf?type=buying`;
                                setTimeout(() => setGeneratingPdf(false), 5000);
                            }}
                            className="bg-brand-primary hover:bg-brand-primary/90 text-white cursor-pointer"
                        >
                            {generatingPdf ? "Se generează…" : "PDF Cumpărare"}
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </AdminLayout>
    );
}
