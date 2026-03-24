import { useTranslation } from "@/hooks/useTranslation";
import { Check } from "@phosphor-icons/react";

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
                                    <div
                                        className={`w-7 h-7 flex items-center justify-center text-xs font-bold ${
                                            isCompleted
                                                ? "bg-brand-secondary text-white"
                                                : isActive
                                                  ? "bg-brand-tertiary text-white"
                                                  : "bg-gray-200 text-gray-500"
                                        }`}
                                    >
                                        {isCompleted ? (
                                            <Check size={14} weight="bold" />
                                        ) : (
                                            stepNum
                                        )}
                                    </div>
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
                                    <div
                                        className={`w-10 md:w-20 h-0.5 mx-3 ${
                                            isCompleted
                                                ? "bg-brand-secondary"
                                                : "bg-gray-200"
                                        }`}
                                    />
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Two-column layout */}
                <div className="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
                    <div className="lg:col-span-3">{children}</div>
                    {sidebar && <div className="lg:col-span-2">{sidebar}</div>}
                </div>
            </div>
        </div>
    );
}
