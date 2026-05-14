import { useTranslation } from "@/hooks/useTranslation";
import { landingAssets } from "@/lib/landingAssets";
import { Check } from "@phosphor-icons/react";
import { ReportType } from "@/types";
import {
    AnimatePresence,
    motion,
    useReducedMotion,
    type Variants,
} from "framer-motion";

const wizardEase = [0.22, 1, 0.36, 1] as const;

const wizardSectionVariants: Variants = {
    hidden: { opacity: 0, y: 14 },
    visible: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.34,
            ease: wizardEase,
            when: "beforeChildren",
            staggerChildren: 0.04,
        },
    },
};

const wizardItemVariants: Variants = {
    hidden: { opacity: 0, y: 10 },
    visible: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.24,
            ease: wizardEase,
        },
    },
};

const stepKeys = [
    "wizard_step_type",
    "wizard_step_property",
    "wizard_step_email",
    "wizard_step_status",
];

const reportTypeDetails: Partial<
    Record<ReportType, { titleKey: string; detailKeys: string[] }>
> = {
    rental_living: {
        titleKey: "rental",
        detailKeys: [
            "landing_rental_detail_1",
            "landing_rental_detail_2",
            "landing_rental_detail_3",
        ],
    },
    rental_business: {
        titleKey: "rental",
        detailKeys: [
            "landing_rental_detail_1",
            "landing_rental_detail_2",
            "landing_rental_detail_3",
        ],
    },
    buying_living: {
        titleKey: "buying",
        detailKeys: [
            "landing_buying_detail_1",
            "landing_buying_detail_2",
            "landing_buying_detail_3",
        ],
    },
    buying_business: {
        titleKey: "buying",
        detailKeys: [
            "landing_buying_detail_1",
            "landing_buying_detail_2",
            "landing_buying_detail_3",
        ],
    },
};

interface WizardLayoutProps {
    currentStep: number;
    children: React.ReactNode;
    sidebar?: React.ReactNode;
    reportType?: ReportType | null;
}

export default function WizardLayout({
    currentStep,
    children,
    sidebar,
    reportType = null,
}: WizardLayoutProps) {
    const { t } = useTranslation();
    const shouldReduceMotion = useReducedMotion();
    const textureImageSrc = landingAssets.textureImageSrc;

    const reportTypeVisuals: Partial<
        Record<ReportType, { imageSrc: string; imageAlt: string }>
    > = {
        rental_living: {
            imageSrc: landingAssets.pricingRentalImageSrc,
            imageAlt: t("landing_pricing_rental_visual_label"),
        },
        buying_living: {
            imageSrc: landingAssets.pricingBuyingImageSrc,
            imageAlt: t("landing_pricing_buying_visual_label"),
        },
    };

    const selectedVisual = reportType ? reportTypeVisuals[reportType] : null;
    const selectedDetails = reportType ? reportTypeDetails[reportType] : null;
    const showSelectedReportCard =
        currentStep === 1 && selectedVisual && selectedDetails;

    const sectionMotionProps = shouldReduceMotion
        ? { initial: false }
        : { initial: "hidden" as const, animate: "visible" as const };

    return (
        <motion.section
            className="relative flex min-h-full flex-1 flex-col overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]"
            variants={wizardSectionVariants}
            {...sectionMotionProps}
        >
            <img
                src={textureImageSrc}
                alt=""
                aria-hidden="true"
                className="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.06] mix-blend-multiply"
            />
            <motion.div
                className="relative mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 py-10 sm:px-6 md:py-14 lg:px-8"
                variants={wizardItemVariants}
            >
                {/* Step Indicator */}
                <motion.div
                    className="mb-10 flex items-center justify-center overflow-x-auto pb-2"
                    variants={wizardItemVariants}
                >
                    {stepKeys.map((key, i) => {
                        const stepNum = i + 1;
                        const isActive = stepNum === currentStep;
                        const isCompleted = stepNum < currentStep;

                        return (
                            <motion.div
                                key={key}
                                className="flex items-center"
                                variants={wizardItemVariants}
                            >
                                <div className="flex items-center gap-2">
                                    <motion.div
                                        initial={false}
                                        animate={{
                                            scale: isActive ? 1.04 : 1,
                                            backgroundColor:
                                                isCompleted || isActive
                                                    ? "#34306A"
                                                    : "#ffffff",
                                            borderColor:
                                                isCompleted || isActive
                                                    ? "#34306A"
                                                    : "#e2e8f0",
                                        }}
                                        transition={{
                                            type: "spring",
                                            stiffness: 300,
                                            damping: 25,
                                        }}
                                        className={`flex h-9 w-9 items-center justify-center border-[1.5px] text-sm font-semibold ${
                                            isCompleted || isActive
                                                ? "text-white"
                                                : "text-brand-primary"
                                        }`}
                                    >
                                        {isCompleted ? (
                                            <motion.div
                                                initial={{
                                                    scale: 0,
                                                    rotate: -90,
                                                }}
                                                animate={{
                                                    scale: 1,
                                                    rotate: 0,
                                                }}
                                                transition={{
                                                    type: "spring",
                                                    stiffness: 300,
                                                    damping: 20,
                                                }}
                                            >
                                                <Check
                                                    size={14}
                                                    weight="bold"
                                                />
                                            </motion.div>
                                        ) : (
                                            stepNum
                                        )}
                                    </motion.div>
                                    <span
                                        className={`hidden text-[14px] leading-[1.3] font-semibold sm:inline ${
                                            isCompleted || isActive
                                                ? "text-brand-primary"
                                                : "text-brand-primary/70"
                                        }`}
                                    >
                                        {t(key)}
                                    </span>
                                </div>
                                {i < stepKeys.length - 1 && (
                                    <div className="relative mx-3 h-px w-10 overflow-hidden bg-[#d7cec3] md:w-20">
                                        <motion.div
                                            initial={{ scaleX: 0 }}
                                            animate={{
                                                scaleX: isCompleted ? 1 : 0,
                                            }}
                                            transition={{
                                                duration: 0.24,
                                                ease: "easeOut",
                                            }}
                                            className="absolute inset-0 origin-left bg-brand-secondary"
                                        />
                                    </div>
                                )}
                            </motion.div>
                        );
                    })}
                </motion.div>

                {/* Two-column layout with animated content */}
                <motion.div
                    className="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1.08fr)_minmax(300px,0.72fr)] lg:gap-8"
                    variants={wizardItemVariants}
                >
                    <AnimatePresence mode="wait" initial={!shouldReduceMotion}>
                        <motion.div
                            key={currentStep}
                            initial={
                                shouldReduceMotion
                                    ? false
                                    : { opacity: 0, y: 12, scale: 0.995 }
                            }
                            animate={{ opacity: 1, y: 0, scale: 1 }}
                            exit={
                                shouldReduceMotion
                                    ? undefined
                                    : { opacity: 0, y: -6, scale: 0.998 }
                            }
                            transition={{
                                duration: 0.26,
                                ease: wizardEase,
                            }}
                            className="border solid-border solid-border-warm bg-white p-6 md:p-8 lg:p-9"
                        >
                            {children}
                        </motion.div>
                    </AnimatePresence>
                    {(selectedVisual || sidebar) && (
                        <AnimatePresence
                            mode="wait"
                            initial={!shouldReduceMotion}
                        >
                            <motion.div
                                key={`sidebar-${currentStep}`}
                                initial={
                                    shouldReduceMotion
                                        ? false
                                        : { opacity: 0, y: 10 }
                                }
                                animate={{ opacity: 1, y: 0 }}
                                exit={
                                    shouldReduceMotion
                                        ? undefined
                                        : { opacity: 0, y: -5 }
                                }
                                transition={{
                                    duration: 0.24,
                                    ease: wizardEase,
                                    delay: shouldReduceMotion ? 0 : 0.02,
                                }}
                                className="space-y-4"
                            >
                                {showSelectedReportCard ? (
                                    <div className="border solid-border solid-border-warm bg-white p-5 shadow-[0_22px_54px_rgba(52,48,106,0.08)] md:p-6">
                                        <div className="flex h-60 items-center justify-center bg-white">
                                            <img
                                                src={selectedVisual.imageSrc}
                                                alt={selectedVisual.imageAlt}
                                                className="h-full w-full object-contain drop-shadow-[0_18px_34px_rgba(52,48,106,0.14)]"
                                            />
                                        </div>
                                        <div className="mt-5 border-t border-brand-primary/10 pt-4">
                                            <p className="text-[13px] font-semibold leading-[1.35] text-brand-primary/64">
                                                {t("sidebar_report_title")}
                                            </p>
                                            <h3 className="mt-1 text-[1.6rem] font-bold leading-[1.02] tracking-[-0.035em] text-brand-primary">
                                                {t(selectedDetails.titleKey)}
                                            </h3>
                                            <ul className="mt-4 space-y-3">
                                                {selectedDetails.detailKeys.map(
                                                    (detailKey) => (
                                                        <li
                                                            key={detailKey}
                                                            className="flex items-start gap-3"
                                                        >
                                                            <span className="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#eef1ff] text-brand-secondary">
                                                                <Check
                                                                    size={12}
                                                                    weight="bold"
                                                                />
                                                            </span>
                                                            <span className="text-[14px] leading-[1.65] text-brand-primary/78">
                                                                {t(detailKey)}
                                                            </span>
                                                        </li>
                                                    ),
                                                )}
                                            </ul>
                                        </div>
                                    </div>
                                ) : null}
                                {!showSelectedReportCard ? sidebar : null}
                            </motion.div>
                        </AnimatePresence>
                    )}
                </motion.div>
            </motion.div>
        </motion.section>
    );
}
