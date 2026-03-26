import { useTranslation } from "@/hooks/useTranslation";
import { Check } from "@phosphor-icons/react";
import { motion, AnimatePresence } from "framer-motion";

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
        <div className="px-4 py-8 md:py-12">
            <div className="max-w-5xl mx-auto">
                {/* Step Indicator */}
                <div className="flex items-center justify-center mb-10">
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
                                            scale: isActive ? 1.1 : 1,
                                            backgroundColor: isCompleted
                                                ? "#f5915d"
                                                : isActive
                                                  ? "#0073f0"
                                                  : "#e5e7eb",
                                        }}
                                        transition={{
                                            type: "spring",
                                            stiffness: 300,
                                            damping: 25,
                                        }}
                                        className={`w-7 h-7 flex items-center justify-center text-xs font-bold ${
                                            isCompleted || isActive
                                                ? "text-white"
                                                : "text-gray-500"
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
                                        className={`text-sm font-semibold hidden sm:inline ${
                                            isActive
                                                ? "text-brand-primary"
                                                : isCompleted
                                                  ? "text-brand-secondary"
                                                  : "text-gray-400"
                                        }`}
                                    >
                                        {t(key)}
                                    </span>
                                </div>
                                {i < stepKeys.length - 1 && (
                                    <div className="relative w-10 md:w-20 h-0.5 mx-3 bg-gray-200 overflow-hidden">
                                        <motion.div
                                            initial={{ scaleX: 0 }}
                                            animate={{
                                                scaleX: isCompleted ? 1 : 0,
                                            }}
                                            transition={{
                                                duration: 0.4,
                                                ease: "easeOut",
                                            }}
                                            className="absolute inset-0 bg-brand-secondary origin-left"
                                        />
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Two-column layout with animated content */}
                <div className="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
                    <motion.div
                        key={currentStep}
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{
                            duration: 0.35,
                            ease: "easeOut",
                        }}
                        className="lg:col-span-3"
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
                            className="lg:col-span-2"
                        >
                            {sidebar}
                        </motion.div>
                    )}
                </div>
            </div>
        </div>
    );
}
