import { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Components/ui/tabs";
import { Textarea } from "@/Components/ui/textarea";
import { Checkbox } from "@/Components/ui/checkbox";
import { Label } from "@/Components/ui/label";
import { Settings as SettingsType } from "@/types";

interface SettingsForm {
    purchase_prompt: string;
    rental_prompt: string;
    commercial_prompt: string;
    auto_send: boolean;
    [key: string]: string | boolean;
}

export default function Settings({ settings }: { settings: SettingsType }) {
    const { errors } = usePage().props;
    const [form, setForm] = useState<SettingsForm>({
        purchase_prompt: settings?.purchase_prompt || "",
        rental_prompt: settings?.rental_prompt || "",
        commercial_prompt: settings?.commercial_prompt || "",
        auto_send: settings?.auto_send || false,
    });
    const [processing, setProcessing] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post("/admin/settings", form, {
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <AdminLayout>
            <Head title="Settings" />

            <h1 className="text-2xl font-bold text-[#0a0a0a] mb-6">Settings</h1>

            <form onSubmit={handleSubmit}>
                <Card className="mb-6">
                    <CardHeader>
                        <CardTitle>AI Prompts</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Tabs defaultValue="purchase">
                            <TabsList className="mb-4">
                                <TabsTrigger value="purchase">
                                    Purchase
                                </TabsTrigger>
                                <TabsTrigger value="rental">Rental</TabsTrigger>
                                <TabsTrigger value="commercial">
                                    Commercial
                                </TabsTrigger>
                            </TabsList>

                            <TabsContent value="purchase">
                                <div className="space-y-2">
                                    <Label htmlFor="purchase_prompt">
                                        Purchase Prompt
                                    </Label>
                                    <Textarea
                                        id="purchase_prompt"
                                        value={form.purchase_prompt}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                purchase_prompt: e.target.value,
                                            })
                                        }
                                        rows={12}
                                        className="font-mono text-xs"
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        This prompt is sent to OpenAI for
                                        purchase report generation. It should
                                        instruct the AI to return valid JSON.
                                    </p>
                                    {errors?.purchase_prompt && (
                                        <p className="text-sm text-red-600">
                                            {errors.purchase_prompt}
                                        </p>
                                    )}
                                </div>
                            </TabsContent>

                            <TabsContent value="rental">
                                <div className="space-y-2">
                                    <Label htmlFor="rental_prompt">
                                        Rental Prompt
                                    </Label>
                                    <Textarea
                                        id="rental_prompt"
                                        value={form.rental_prompt}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                rental_prompt: e.target.value,
                                            })
                                        }
                                        rows={12}
                                        className="font-mono text-xs"
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        This prompt is sent to OpenAI for rental
                                        report generation. It should instruct
                                        the AI to return valid JSON.
                                    </p>
                                    {errors?.rental_prompt && (
                                        <p className="text-sm text-red-600">
                                            {errors.rental_prompt}
                                        </p>
                                    )}
                                </div>
                            </TabsContent>

                            <TabsContent value="commercial">
                                <div className="space-y-2">
                                    <Label htmlFor="commercial_prompt">
                                        Commercial Prompt
                                    </Label>
                                    <Textarea
                                        id="commercial_prompt"
                                        value={form.commercial_prompt}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                commercial_prompt:
                                                    e.target.value,
                                            })
                                        }
                                        rows={12}
                                        className="font-mono text-xs"
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        This prompt is sent to OpenAI for
                                        commercial report generation. It should
                                        instruct the AI to return valid JSON.
                                    </p>
                                    {errors?.commercial_prompt && (
                                        <p className="text-sm text-red-600">
                                            {errors.commercial_prompt}
                                        </p>
                                    )}
                                </div>
                            </TabsContent>
                        </Tabs>
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
                                Automatically send reports after generation
                            </Label>
                        </div>
                    </CardContent>
                </Card>

                <Button
                    type="submit"
                    disabled={processing}
                    className="bg-[#1a56db] hover:bg-[#1a56db]/90 text-white cursor-pointer"
                >
                    {processing ? "Saving…" : "Save Settings"}
                </Button>
            </form>
        </AdminLayout>
    );
}
