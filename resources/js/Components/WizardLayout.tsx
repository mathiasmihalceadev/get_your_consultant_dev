import { useTranslation } from "@/hooks/useTranslation";
import { Check } from "@phosphor-icons/react";
import { motion } from "framer-motion";

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
}

export default function WizardLayout({
    currentStep,
    children,
    sidebar,
}: WizardLayoutProps) {
    const { t } = useTranslation();

    return (
        <section className="border-b solid-divider bg-[linear-gradient(180deg,#ffffff_0%,#fff7f1_100%)]">
            <div className="mx-auto max-w-6xl px-4 py-10 sm:px-6 md:py-14 lg:px-8">
                {/* Step Indicator */}
                <div className="mb-10 flex items-center justify-center overflow-x-auto pb-2">
                    {stepKeys.map((key, i) => {
                        const stepNum = i + 1;
                        const isActive = stepNum === currentStep;
                        const isCompleted = stepNum < currentStep;

                        return (
                            <div key={key} className="flex items-center">
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
                                                duration: 0.4,
                                                ease: "easeOut",
                                            }}
                                            className="absolute inset-0 origin-left bg-brand-secondary"
                                        />
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Two-column layout with animated content */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1.08fr)_minmax(300px,0.72fr)] lg:gap-8">
                    <motion.div
                        key={currentStep}
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{
                            duration: 0.35,
                            ease: "easeOut",
                        }}
                        className="border solid-border solid-border-warm bg-white p-6 md:p-8"
                    >
                        {children}
                    </motion.div>
                    {sidebar && (
                        <motion.div
                            key={`sidebar-${currentStep}`}
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{
                                duration: 0.35,
                                ease: "easeOut",
                                delay: 0.1,
                            }}
                            className="space-y-4"
                        >
                            {sidebar}
                        </motion.div>
                    )}
                </div>
            </div>
        </section>
    );
}
