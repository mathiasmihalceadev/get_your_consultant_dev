import { useEffect, useRef, useState } from "react";
import { Head, router } from "@inertiajs/react";
import {
    ArrowClockwise,
    ArrowRight,
    CheckCircle,
    Equals,
    MagnifyingGlass,
    TrendDown,
    TrendUp,
    WarningCircle,
} from "@phosphor-icons/react";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";

type EstimateValuation =
    | "under_estimated"
    | "close_to_estimated"
    | "over_estimated";

type EstimateResult = {
    report_id: number;
    url: string;
    report_type: "buying_living" | "rental_living";
    transaction_type: "buying" | "renting";
    valuation: EstimateValuation;
    result: "Under estimated value" | "Close to the estimated value" | "Over the estimated value";
    asking_price: number | string | null;
    estimated_value: number | string | null;
    currency: string | null;
    difference_percent: number | string | null;
    confidence: "low" | "medium" | "high" | string | null;
};

interface HeroEstimateCheckProps {
    initialUrl: string;
    recaptchaSiteKey?: string | null;
}

function isValidUrl(value: string): boolean {
    try {
        const parsed = new URL(value);
        return parsed.protocol === "http:" || parsed.protocol === "https:";
    } catch {
        return false;
    }
}

function waitForRecaptcha(): Promise<NonNullable<typeof window.grecaptcha>> {
    return new Promise((resolve, reject) => {
        let attempts = 0;

        const check = () => {
            if (window.grecaptcha) {
                resolve(window.grecaptcha);
                return;
            }

            attempts += 1;

            if (attempts > 50) {
                reject(new Error("reCAPTCHA did not load."));
                return;
            }

            window.setTimeout(check, 100);
        };

        check();
    });
}

export default function HeroEstimateCheck({
    initialUrl,
    recaptchaSiteKey,
}: HeroEstimateCheckProps) {
    const { t, locale, localePath } = useTranslation();
    const [status, setStatus] = useState<"loading" | "success" | "error">(
        "loading",
    );
    const [result, setResult] = useState<EstimateResult | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const startedRef = useRef(false);

    useEffect(() => {
        if (startedRef.current) {
            return;
        }

        startedRef.current = true;

        const runAnalysis = async () => {
            const url = initialUrl.trim();

            if (!isValidUrl(url)) {
                setStatus("error");
                setErrorMessage(t("wizard_url_invalid"));
                return;
            }

            try {
                let recaptchaToken: string | null = null;

                if (recaptchaSiteKey) {
                    const grecaptcha = await waitForRecaptcha();

                    recaptchaToken = await new Promise<string>(
                        (resolve, reject) => {
                            grecaptcha.ready(() => {
                                grecaptcha
                                    .execute(recaptchaSiteKey, {
                                        action: "landing_estimate_check",
                                    })
                                    .then(resolve)
                                    .catch(reject);
                            });
                        },
                    );
                }

                const response = await window.axios.post<EstimateResult>(
                    localePath("/estimate-check/analyze"),
                    {
                        url,
                        recaptcha_token: recaptchaToken,
                    },
                );

                setResult(response.data);
                setStatus("success");
            } catch (error: any) {
                const message =
                    error?.response?.data?.message ||
                    t("hero_estimate_error_generic");

                setErrorMessage(message);
                setStatus("error");
            }
        };

        void runAnalysis();
    }, [initialUrl, localePath, recaptchaSiteKey, t]);

    const valuationConfig = result
        ? {
              under_estimated: {
                  label: t("hero_estimate_under_label"),
                  icon: TrendDown,
                  accent: "text-[#16834f]",
                  bg: "bg-[#eaf8f0]",
                  border: "border-[#16834f]/20",
              },
              close_to_estimated: {
                  label: t("hero_estimate_close_label"),
                  icon: Equals,
                  accent: "text-[#b86f12]",
                  bg: "bg-[#fff4df]",
                  border: "border-[#f3b44f]/40",
              },
              over_estimated: {
                  label: t("hero_estimate_over_label"),
                  icon: TrendUp,
                  accent: "text-[#b42318]",
                  bg: "bg-[#fff1f0]",
                  border: "border-[#b42318]/20",
              },
          }[result.valuation]
        : null;

    const ValuationIcon = valuationConfig?.icon ?? CheckCircle;
    return (
        <PublicLayout>
            <Head title={t("hero_estimate_meta_title")}>
                <meta name="robots" content="noindex, nofollow" />
                {recaptchaSiteKey && (
                    <script
                        src={`https://www.google.com/recaptcha/api.js?render=${recaptchaSiteKey}`}
                    />
                )}
            </Head>

            <section className="relative flex-1 overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] py-16 md:py-20">
                <div className="pointer-events-none absolute inset-0 bg-[url('/images/blue-noise-texture.png')] bg-cover opacity-[0.055] mix-blend-multiply" />

                <div className="relative mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(0,0.92fr)_minmax(320px,0.72fr)] lg:items-start lg:px-8">
                    <div className="border border-brand-primary/10 bg-white p-6 shadow-[0_18px_42px_rgba(52,48,106,0.08)] md:p-8">
                        {status === "loading" && (
                            <>
                                <div className="flex h-12 w-12 items-center justify-center bg-brand-primary text-white">
                                    <MagnifyingGlass
                                        size={24}
                                        weight="bold"
                                        className="animate-pulse"
                                    />
                                </div>
                                <h1 className="mt-6 text-[2.1rem] font-bold leading-[0.98] tracking-[-0.04em] text-brand-primary md:text-[2.8rem]">
                                    {t("hero_estimate_loading_title")}
                                </h1>
                                <p className="mt-4 max-w-2xl text-[14px] leading-[1.7] text-brand-primary/76 md:text-base">
                                    {t("hero_estimate_loading_desc")}
                                </p>
                                <div className="mt-8 h-2 overflow-hidden bg-brand-primary/10">
                                    <div className="h-full w-1/2 animate-[pulse_1.4s_ease-in-out_infinite] bg-brand-primary" />
                                </div>
                            </>
                        )}

                        {status === "error" && (
                            <>
                                <div className="flex h-12 w-12 items-center justify-center bg-[#fff4df] text-[#b86f12]">
                                    <WarningCircle size={24} weight="fill" />
                                </div>
                                <h1 className="mt-6 text-[2.1rem] font-bold leading-[0.98] tracking-[-0.04em] text-brand-primary md:text-[2.8rem]">
                                    {t("hero_estimate_error_title")}
                                </h1>
                                <p className="mt-4 max-w-2xl text-[14px] leading-[1.7] text-brand-primary/76 md:text-base">
                                    {errorMessage}
                                </p>
                                <button
                                    type="button"
                                    onClick={() => router.visit(localePath("/"))}
                                    className="mt-8 inline-flex cursor-pointer items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92"
                                >
                                    {t("hero_estimate_try_another")}
                                    <ArrowClockwise size={16} />
                                </button>
                            </>
                        )}

                        {status === "success" && result && valuationConfig && (
                            <>
                                <div className={`inline-flex items-center gap-2 border px-3 py-2 text-sm font-semibold ${valuationConfig.border} ${valuationConfig.bg} ${valuationConfig.accent}`}>
                                    <ValuationIcon size={18} weight="bold" />
                                    {t("hero_estimate_result_label")}
                                </div>

                                <h1 className={`mt-6 text-[2.1rem] font-bold leading-[0.98] tracking-[-0.04em] md:text-[2.8rem] ${valuationConfig.accent}`}>
                                    {valuationConfig.label}
                                </h1>

                                <p className="mt-4 max-w-2xl text-[14px] leading-[1.7] text-brand-primary/76 md:text-base">
                                    {t("hero_estimate_success_desc")}
                                </p>

                                <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                                    <button
                                        type="button"
                                        onClick={() =>
                                            router.visit(
                                                localePath("/submit-email"),
                                            )
                                        }
                                        className="inline-flex cursor-pointer items-center justify-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92"
                                    >
                                        {t("hero_estimate_full_report_cta")}
                                        <ArrowRight size={16} />
                                    </button>
                                </div>
                            </>
                        )}
                    </div>

                    <aside className="space-y-4">
                        <div className="border border-brand-primary/10 bg-brand-primary p-6 text-white shadow-[0_18px_44px_rgba(52,48,106,0.16)]">
                            <h2 className="text-lg font-semibold">
                                {t("hero_estimate_sidebar_title")}
                            </h2>
                            <div className="mt-5 space-y-3">
                                {[
                                    t("hero_estimate_sidebar_item_1"),
                                    t("hero_estimate_sidebar_item_2"),
                                ].map((item) => (
                                    <div
                                        key={item}
                                        className="flex items-start gap-3"
                                    >
                                        <CheckCircle
                                            size={18}
                                            weight="fill"
                                            className="mt-0.5 shrink-0 text-brand-secondary"
                                        />
                                        <p className="text-[14px] leading-[1.65] text-white/80">
                                            {item}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="border border-brand-primary/10 bg-white p-6 text-brand-primary">
                            <p className="text-[14px] leading-[1.7] text-brand-primary/74">
                                {t("hero_estimate_disclaimer")}
                            </p>
                        </div>
                    </aside>
                </div>
            </section>
        </PublicLayout>
    );
}
