import { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import PublicLayout from "@/Layouts/PublicLayout";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import { ReportType, PageProps } from "@/types";
import { CheckCircle } from "@phosphor-icons/react";

type FeedbackLocale = "ro" | "en";

interface FeedbackReport {
    id: number;
    report_type: ReportType;
    locale: FeedbackLocale;
    url: string;
}

interface FeedbackProps {
    pageToken: string;
    report: FeedbackReport;
    submitted: boolean;
    errors: Record<string, string>;
}

const ratingOptions = Array.from({ length: 10 }, (_, index) => index + 1);

const copies = {
    ro: {
        pageTitle: "Feedback raport GetYourConsultant",
        submittedTitle: "Mul\u021Bumim pentru feedback!",
        submittedBody: "R\u0103spunsurile tale au fost \u00EEnregistrate.",
        title: "Cum \u021Bi s-a p\u0103rut raportul GetYourConsultant?",
        intro:
            "Mul\u021Bumim c\u0103 ai folosit GetYourConsultant\u2122. \u00CEncerc\u0103m s\u0103 \u00EEmbun\u0103t\u0103\u021Bim constant platforma \u0219i feedback-ul t\u0103u ne ajut\u0103 enorm.",
        ratingLabel: "Ce not\u0103 ai acorda raportului?",
        ratingHint: "Alege o not\u0103 de la 1 la 10.",
        usefulLabel:
            "Care informa\u021Bie \u021Bi s-a p\u0103rut cea mai util\u0103?",
        extraLabel: "Ce ai vrea s\u0103 vezi \u00EEn plus \u00EEn raport?",
        recommendLabel: "Ai recomanda platforma unui prieten?",
        yes: "DA",
        no: "NU",
        trustLabel:
            "Ce te-ar face s\u0103 ai \u0219i mai mult\u0103 \u00EEncredere \u00EEn acest raport?",
        sending: "Se trimite...",
        submit: "Trimite feedback",
    },
    en: {
        pageTitle: "GetYourConsultant Report Feedback",
        submittedTitle: "Thank you for your feedback!",
        submittedBody: "Your answers have been recorded.",
        title: "How was your GetYourConsultant report?",
        intro:
            "Thank you for using GetYourConsultant\u2122. We are constantly improving the platform, and your feedback helps us a lot.",
        ratingLabel: "What rating would you give the report?",
        ratingHint: "Choose a rating from 1 to 10.",
        usefulLabel: "Which information did you find most useful?",
        extraLabel: "What would you like to see added to the report?",
        recommendLabel: "Would you recommend the platform to a friend?",
        yes: "YES",
        no: "NO",
        trustLabel: "What would make you trust this report even more?",
        sending: "Sending...",
        submit: "Send feedback",
    },
} as const;

export default function Feedback({
    pageToken,
    report,
    submitted,
    errors,
}: FeedbackProps) {
    const { flash } = usePage<PageProps>().props;
    const copy = copies[report.locale === "en" ? "en" : "ro"];
    const [rating, setRating] = useState<number | null>(null);
    const [mostUsefulInfo, setMostUsefulInfo] = useState("");
    const [wantedExtra, setWantedExtra] = useState("");
    const [wouldRecommend, setWouldRecommend] = useState<boolean | null>(null);
    const [trustImprovement, setTrustImprovement] = useState("");
    const [processing, setProcessing] = useState(false);

    const canSubmit =
        rating !== null &&
        mostUsefulInfo.trim() !== "" &&
        wouldRecommend !== null &&
        !processing;

    const handleSubmit = (event: React.FormEvent) => {
        event.preventDefault();

        if (!canSubmit) {
            return;
        }

        setProcessing(true);
        router.post(
            `/feedback/${pageToken}`,
            {
                rating,
                most_useful_info: mostUsefulInfo,
                wanted_extra: wantedExtra,
                would_recommend: wouldRecommend,
                trust_improvement: trustImprovement,
            },
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    };

    return (
        <PublicLayout>
            <Head title={copy.pageTitle} />

            <main className="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="border border-brand-primary/10 bg-white p-6 shadow-sm sm:p-8">
                    {submitted ? (
                        <div className="text-center">
                            <CheckCircle
                                size={56}
                                weight="fill"
                                className="mx-auto text-emerald-600"
                            />
                            <h1 className="mt-5 text-2xl font-semibold text-brand-primary">
                                {copy.submittedTitle}
                            </h1>
                            <p className="mt-3 text-sm leading-6 text-brand-primary/70">
                                {copy.submittedBody}
                            </p>
                        </div>
                    ) : (
                        <>
                            {flash.success && (
                                <div className="mb-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                    {flash.success}
                                </div>
                            )}

                            <h1 className="text-3xl font-semibold leading-tight text-brand-primary">
                                {copy.title}
                            </h1>
                            <p className="mt-4 text-sm leading-6 text-brand-primary/72">
                                {copy.intro}
                            </p>
                            <p className="mt-3 break-all text-xs leading-5 text-brand-primary/50">
                                {report.url}
                            </p>

                            <form
                                onSubmit={handleSubmit}
                                className="mt-8 space-y-7"
                            >
                                <div>
                                    <Label className="text-base font-semibold text-brand-primary">
                                        {copy.ratingLabel}
                                    </Label>
                                    <p className="mt-1 text-sm text-brand-primary/60">
                                        {copy.ratingHint}
                                    </p>
                                    <div className="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-10">
                                        {ratingOptions.map((value) => (
                                            <button
                                                key={value}
                                                type="button"
                                                onClick={() =>
                                                    setRating(value)
                                                }
                                                className={`h-11 border text-sm font-semibold transition-colors ${
                                                    rating === value
                                                        ? "border-brand-primary bg-brand-primary text-white"
                                                        : "border-brand-primary/15 text-brand-primary hover:bg-brand-primary/4"
                                                }`}
                                            >
                                                {value}
                                            </button>
                                        ))}
                                    </div>
                                    {errors?.rating && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {errors.rating}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor="most_useful_info"
                                        className="text-base font-semibold text-brand-primary"
                                    >
                                        {copy.usefulLabel}
                                    </Label>
                                    <Textarea
                                        id="most_useful_info"
                                        value={mostUsefulInfo}
                                        onChange={(event) =>
                                            setMostUsefulInfo(
                                                event.target.value,
                                            )
                                        }
                                        rows={4}
                                        className="rounded-none border-brand-primary/15"
                                        required
                                    />
                                    {errors?.most_useful_info && (
                                        <p className="text-sm text-red-600">
                                            {errors.most_useful_info}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor="wanted_extra"
                                        className="text-base font-semibold text-brand-primary"
                                    >
                                        {copy.extraLabel}
                                    </Label>
                                    <Textarea
                                        id="wanted_extra"
                                        value={wantedExtra}
                                        onChange={(event) =>
                                            setWantedExtra(event.target.value)
                                        }
                                        rows={4}
                                        className="rounded-none border-brand-primary/15"
                                    />
                                    {errors?.wanted_extra && (
                                        <p className="text-sm text-red-600">
                                            {errors.wanted_extra}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <Label className="text-base font-semibold text-brand-primary">
                                        {copy.recommendLabel}
                                    </Label>
                                    <div className="mt-3 flex gap-3">
                                        {[
                                            { label: copy.yes, value: true },
                                            { label: copy.no, value: false },
                                        ].map((option) => (
                                            <button
                                                key={option.label}
                                                type="button"
                                                onClick={() =>
                                                    setWouldRecommend(
                                                        option.value,
                                                    )
                                                }
                                                className={`h-11 min-w-24 border px-5 text-sm font-semibold transition-colors ${
                                                    wouldRecommend ===
                                                    option.value
                                                        ? "border-brand-primary bg-brand-primary text-white"
                                                        : "border-brand-primary/15 text-brand-primary hover:bg-brand-primary/4"
                                                }`}
                                            >
                                                {option.label}
                                            </button>
                                        ))}
                                    </div>
                                    {errors?.would_recommend && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {errors.would_recommend}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor="trust_improvement"
                                        className="text-base font-semibold text-brand-primary"
                                    >
                                        {copy.trustLabel}
                                    </Label>
                                    <Textarea
                                        id="trust_improvement"
                                        value={trustImprovement}
                                        onChange={(event) =>
                                            setTrustImprovement(
                                                event.target.value,
                                            )
                                        }
                                        rows={4}
                                        className="rounded-none border-brand-primary/15"
                                    />
                                    {errors?.trust_improvement && (
                                        <p className="text-sm text-red-600">
                                            {errors.trust_improvement}
                                        </p>
                                    )}
                                </div>

                                <div className="flex justify-end">
                                    <Button
                                        type="submit"
                                        disabled={!canSubmit}
                                        className="h-11 rounded-none bg-brand-primary px-5 text-white hover:bg-brand-primary/92 disabled:opacity-50"
                                    >
                                        {processing
                                            ? copy.sending
                                            : copy.submit}
                                    </Button>
                                </div>
                            </form>
                        </>
                    )}
                </div>
            </main>
        </PublicLayout>
    );
}
