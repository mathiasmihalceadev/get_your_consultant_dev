import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Badge } from "@/Components/ui/badge";
import { ArrowLeft } from "@phosphor-icons/react";
import { Report, ReportType } from "@/types";

const typeLabels: Record<ReportType, string> = {
    purchase: "Purchase Report",
    rental: "Rental Report",
    commercial: "Commercial Report",
};

interface SubmitEmailProps {
    report: Pick<Report, "id" | "url" | "report_type">;
    errors: Record<string, string>;
}

export default function SubmitEmail({ report, errors }: SubmitEmailProps) {
    const [email, setEmail] = useState("");
    const [processing, setProcessing] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        router.post(
            "/submit-email",
            {
                email,
                report_id: report.id,
            },
            {
                onFinish: () => setProcessing(false),
            },
        );
    };

    return (
        <>
            <Head title="Enter Your Email" />
            <div className="min-h-screen bg-white flex items-center justify-center px-4 py-16">
                <div className="max-w-md w-full">
                    <a
                        href="/"
                        onClick={(e) => {
                            e.preventDefault();
                            router.visit("/");
                        }}
                        className="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-[#0a0a0a] mb-8 transition-colors"
                    >
                        <ArrowLeft size={16} />
                        Start over
                    </a>

                    <h1 className="text-3xl font-bold text-[#0a0a0a] mb-4">
                        Almost there!
                    </h1>

                    <div className="flex flex-wrap gap-2 mb-2">
                        <Badge variant="secondary" className="text-xs">
                            {typeLabels[report.report_type]}
                        </Badge>
                    </div>
                    <p className="text-sm text-muted-foreground mb-6 break-all">
                        {report.url}
                    </p>

                    <form onSubmit={handleSubmit} className="space-y-4 mb-6">
                        <div>
                            <Label
                                htmlFor="email"
                                className="text-sm font-medium mb-1.5 block"
                            >
                                Your email address
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                placeholder="you@example.com"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                                className={
                                    errors?.email ? "border-red-500" : ""
                                }
                            />
                            {errors?.email && (
                                <p className="text-sm text-red-600 mt-1.5">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <Button
                            type="submit"
                            disabled={processing || !email}
                            className="w-full bg-[#1a56db] hover:bg-[#1a56db]/90 text-white cursor-pointer"
                        >
                            {processing ? "Submitting…" : "Get My Report"}
                        </Button>
                    </form>

                    <p className="text-xs text-muted-foreground text-center">
                        You will receive your report within 24 hours.
                    </p>
                </div>
            </div>
        </>
    );
}
