import { Head, useForm, usePage } from "@inertiajs/react";
import {
    EnvelopeSimple,
    MapPinLine,
    ClockCountdown,
} from "@phosphor-icons/react";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import PublicLayout from "@/Layouts/PublicLayout";
import { useTranslation } from "@/hooks/useTranslation";
import { landingAssets } from "@/lib/landingAssets";
import { PageProps } from "@/types";

export default function Contact() {
    const { t, localePath } = useTranslation();
    const { flash } = usePage<PageProps>().props;
    const textureImageSrc = landingAssets.textureImageSrc;
    const form = useForm({
        name: "",
        email: "",
        subject: "",
        message: "",
    });

    const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        form.post(localePath("/contact"), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <PublicLayout>
            <Head title={t("contact")} />

            <section className="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]">
                <img
                    src={textureImageSrc}
                    alt=""
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.06] mix-blend-multiply"
                />

                <div className="relative mx-auto max-w-6xl px-4 py-12 sm:px-6 md:py-16 lg:px-8">
                    <div className="max-w-3xl">
                        <h1 className="text-[2.45rem] leading-[0.98] font-extrabold tracking-[-0.05em] text-brand-primary md:text-[3.5rem] md:leading-[0.95]">
                            {t("contact_page_title")}
                        </h1>
                        <p className="mt-4 max-w-2xl text-[14px] leading-[1.7] text-brand-primary/78 md:text-base">
                            {t("contact_page_desc")}
                        </p>
                    </div>

                    <div className="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1.08fr)_minmax(300px,0.72fr)] lg:gap-8">
                        <div className="border solid-border solid-border-warm bg-white p-6 md:p-8 lg:p-9">
                            <h2 className="text-[1.55rem] font-bold tracking-[-0.03em] text-brand-primary md:text-[1.9rem]">
                                {t("contact_form_card_title")}
                            </h2>
                            <p className="mt-2 text-[14px] leading-[1.68] text-brand-primary/76">
                                {t("contact_form_required_note")}
                            </p>

                            {flash.success && (
                                <div className="mt-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                    {flash.success}
                                </div>
                            )}

                            <form
                                onSubmit={handleSubmit}
                                className="mt-6 space-y-4"
                            >
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <Label
                                            htmlFor="name"
                                            className="mb-1.5 block text-sm font-medium"
                                        >
                                            {t("contact_form_name")}
                                        </Label>
                                        <Input
                                            id="name"
                                            value={form.data.name}
                                            onChange={(event) =>
                                                form.setData(
                                                    "name",
                                                    event.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                "contact_form_name_placeholder",
                                            )}
                                            className={
                                                form.errors.name
                                                    ? "border-red-500"
                                                    : "border-brand-primary/30"
                                            }
                                        />
                                        {form.errors.name && (
                                            <p className="mt-1.5 text-sm text-red-600">
                                                {form.errors.name}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <Label
                                            htmlFor="email"
                                            className="mb-1.5 block text-sm font-medium"
                                        >
                                            {t("contact_form_email")}
                                        </Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            value={form.data.email}
                                            onChange={(event) =>
                                                form.setData(
                                                    "email",
                                                    event.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                "contact_form_email_placeholder",
                                            )}
                                            className={
                                                form.errors.email
                                                    ? "border-red-500"
                                                    : "border-brand-primary/30"
                                            }
                                        />
                                        {form.errors.email && (
                                            <p className="mt-1.5 text-sm text-red-600">
                                                {form.errors.email}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <Label
                                        htmlFor="subject"
                                        className="mb-1.5 block text-sm font-medium"
                                    >
                                        {t("contact_form_subject")}
                                    </Label>
                                    <Input
                                        id="subject"
                                        value={form.data.subject}
                                        onChange={(event) =>
                                            form.setData(
                                                "subject",
                                                event.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            "contact_form_subject_placeholder",
                                        )}
                                        className={
                                            form.errors.subject
                                                ? "border-red-500"
                                                : "border-brand-primary/30"
                                        }
                                    />
                                    {form.errors.subject && (
                                        <p className="mt-1.5 text-sm text-red-600">
                                            {form.errors.subject}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <Label
                                        htmlFor="message"
                                        className="mb-1.5 block text-sm font-medium"
                                    >
                                        {t("contact_form_message")}
                                    </Label>
                                    <Textarea
                                        id="message"
                                        value={form.data.message}
                                        onChange={(event) =>
                                            form.setData(
                                                "message",
                                                event.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            "contact_form_message_placeholder",
                                        )}
                                        className={`min-h-40 ${form.errors.message ? "border-red-500" : "border-brand-primary/30"}`}
                                    />
                                    {form.errors.message && (
                                        <p className="mt-1.5 text-sm text-red-600">
                                            {form.errors.message}
                                        </p>
                                    )}
                                </div>

                                <div className="flex justify-end pt-2">
                                    <button
                                        type="submit"
                                        disabled={form.processing}
                                        className="inline-flex cursor-pointer items-center gap-2 bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary/92 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <EnvelopeSimple
                                            size={16}
                                            weight="bold"
                                        />
                                        {form.processing
                                            ? t("submitting")
                                            : t("contact_form_submit")}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div className="space-y-4">
                            <div className="border solid-border solid-border-warm bg-white p-6">
                                <div className="flex items-start gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]">
                                        <MapPinLine
                                            size={22}
                                            weight="fill"
                                            className="text-brand-tertiary"
                                        />
                                    </div>
                                    <div>
                                        <h3 className="mb-1 text-[0.95rem] font-semibold text-brand-primary">
                                            {t("contact_sidebar_address_title")}
                                        </h3>
                                        <p className="whitespace-pre-line text-[14px] leading-[1.68] text-brand-primary/78">
                                            {t("landing_footer_address")}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="border solid-border solid-border-warm bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)] p-6">
                                <div className="flex items-start gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-white">
                                        <ClockCountdown
                                            size={22}
                                            weight="fill"
                                            className="text-brand-secondary"
                                        />
                                    </div>
                                    <div>
                                        <h3 className="mb-1 text-[0.95rem] font-semibold text-brand-primary">
                                            {t(
                                                "contact_sidebar_response_title",
                                            )}
                                        </h3>
                                        <p className="text-[14px] leading-[1.68] text-brand-primary/78">
                                            {t("contact_sidebar_response_desc")}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
