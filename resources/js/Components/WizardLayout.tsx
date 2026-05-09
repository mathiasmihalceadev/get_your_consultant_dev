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

    const sectionMotionProps = shouldReduceMotion
        ? { initial: false }
        : { initial: "hidden" as const, animate: "visible" as const };

    return (
        <motion.section
            className="relative overflow-hidden border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#f2f5ff_100%)]"
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
                className="relative mx-auto max-w-6xl px-4 py-10 sm:px-6 md:py-14 lg:px-8"
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
                                        className={`hidden text-sm font-semibold sm:inline ${
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
                            className="border solid-border solid-border-warm bg-white p-6 md:p-8"
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
                                {selectedVisual ? (
                                    <div className="relative overflow-hidden border solid-border solid-border-warm bg-white p-4">
                                        <div className="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.82),rgba(242,245,255,0.94))]" />
                                        <div className="absolute inset-0 opacity-[0.08] mix-blend-multiply">
                                            <img
                                                src={textureImageSrc}
                                                alt=""
                                                aria-hidden="true"
                                                className="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div className="relative h-56 overflow-hidden">
                                            <div className="absolute inset-x-8 bottom-3 h-12 rounded-full bg-brand-primary/12 blur-2xl" />
                                            <img
                                                src={selectedVisual.imageSrc}
                                                alt={selectedVisual.imageAlt}
                                                className="relative z-10 h-full w-full scale-[1.05] object-contain drop-shadow-[0_22px_38px_rgba(52,48,106,0.18)]"
                                            />
                                        </div>
                                    </div>
                                ) : null}
                                {!selectedVisual ? sidebar : null}
                            </motion.div>
                        </AnimatePresence>
                    )}
                </motion.div>
            </motion.div>
        </motion.section>
    );
}
