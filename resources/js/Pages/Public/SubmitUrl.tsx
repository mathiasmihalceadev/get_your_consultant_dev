import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Card, CardContent } from "@/Components/ui/card";
import {
    ArrowLeft,
    MagnifyingGlass,
    Envelope,
    FilePdf,
} from "@phosphor-icons/react";
import { ReportType } from "@/types";

const typeLabels: Record<ReportType, string> = {
    purchase: "Purchase Report",
    rental: "Rental Report",
    commercial: "Commercial Report",
};

interface SubmitUrlProps {
    reportType: ReportType;
    errors: Record<string, string>;
}

export default function SubmitUrl({ reportType, errors }: SubmitUrlProps) {
    const [url, setUrl] = useState("");
    const [processing, setProcessing] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post(
            "/validate-url",
            {
                url,
                report_type: reportType,
            },
            {
                onFinish: () => setProcessing(false),
            },
        );
    };

    return (
        <>
            <Head title={typeLabels[reportType]} />
            <div className="min-h-screen bg-white flex items-center justify-center px-4 py-16">
                <div className="max-w-xl w-full">
                    <a
                        href="/"
                        onClick={(e) => {
                            e.preventDefault();
                            router.visit("/");
                        }}
                        className="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-[#0a0a0a] mb-8 transition-colors"
                    >
                        <ArrowLeft size={16} />
                        Back to selection
                    </a>

                    <h1 className="text-3xl md:text-4xl font-bold text-[#0a0a0a] mb-2">
                        {typeLabels[reportType]}
                    </h1>
                    <p className="text-muted-foreground mb-8">
                        Enter the property listing URL to generate your report.
                    </p>

                    <form onSubmit={handleSubmit} className="space-y-4 mb-12">
                        <div>
                            <Label
                                htmlFor="url"
                                className="text-sm font-medium mb-1.5 block"
                            >
                                Property listing URL
                            </Label>
                            <Input
                                id="url"
                                type="url"
                                placeholder="https://example.com/property/123"
                                value={url}
                                onChange={(e) => setUrl(e.target.value)}
                                required
                                className={errors?.url ? "border-red-500" : ""}
                            />
                            {errors?.url && (
                                <p className="text-sm text-red-600 mt-1.5">
                                    {errors.url}
                                </p>
                            )}
                        </div>

                        <Button
                            type="submit"
                            disabled={processing || !url}
                            className="w-full bg-[#1a56db] hover:bg-[#1a56db]/90 text-white cursor-pointer"
                        >
                            {processing ? "Validating…" : "Submit URL"}
                        </Button>
                    </form>

                    <div>
                        <h2 className="text-lg font-semibold text-[#0a0a0a] mb-6">
                            How it works
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {[
                                {
                                    icon: MagnifyingGlass,
                                    step: "1",
                                    title: "Submit URL",
                                    desc: "Paste the property listing URL for analysis.",
                                },
                                {
                                    icon: Envelope,
                                    step: "2",
                                    title: "Confirm your email",
                                    desc: "Enter your email to receive the report.",
                                },
                                {
                                    icon: FilePdf,
                                    step: "3",
                                    title: "Receive report",
                                    desc: "Get a detailed PDF report via email.",
                                },
                            ].map(({ icon: Icon, step, title, desc }) => (
                                <Card key={step} className="text-center">
                                    <CardContent className="pt-6">
                                        <div className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#1a56db]/10 text-[#1a56db] mb-3">
                                            <Icon size={20} weight="bold" />
                                        </div>
                                        <h3 className="font-medium text-sm mb-1">
                                            {title}
                                        </h3>
                                        <p className="text-xs text-muted-foreground">
                                            {desc}
                                        </p>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
