import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { House, Key, Buildings, Icon } from "@phosphor-icons/react";
import { ReportType } from "@/types";

interface ReportTypeOption {
    type: ReportType;
    title: string;
    description: string;
    icon: Icon;
}

const reportTypes: ReportTypeOption[] = [
    {
        type: "purchase",
        title: "Purchase Report",
        description:
            "Full analysis for buying a property. Price evaluation, investment potential, risk assessment.",
        icon: House,
    },
    {
        type: "rental",
        title: "Rental Report",
        description:
            "Evaluate a rental listing. Fair rent analysis, livability score, hidden costs.",
        icon: Key,
    },
    {
        type: "commercial",
        title: "Commercial Report",
        description:
            "Analyse commercial spaces. Foot traffic, zoning, investment yield.",
        icon: Buildings,
    },
];

export default function Index() {
    const [selected, setSelected] = useState<ReportType | null>(null);

    const handleContinue = () => {
        if (selected) {
            router.visit(`/submit-url?type=${selected}`);
        }
    };

    return (
        <>
            <Head title="Get Your Property Report" />
            <div className="min-h-screen bg-white flex items-center justify-center px-4 py-16">
                <div className="max-w-4xl w-full text-center">
                    <h1 className="text-4xl md:text-5xl font-bold text-[#0a0a0a] mb-3">
                        Get Your Property Report
                    </h1>
                    <p className="text-lg text-muted-foreground mb-12">
                        Choose the type of analysis you need.
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                        {reportTypes.map(
                            ({ type, title, description, icon: Icon }) => (
                                <Card
                                    key={type}
                                    className={`cursor-pointer transition-all duration-200 hover:shadow-lg ${
                                        selected === type
                                            ? "border-[#1a56db] border-2 shadow-lg"
                                            : "border-border hover:border-[#1a56db]/50"
                                    }`}
                                    onClick={() => setSelected(type)}
                                >
                                    <CardHeader className="items-center pt-8">
                                        <Icon
                                            size={48}
                                            weight={
                                                selected === type
                                                    ? "fill"
                                                    : "regular"
                                            }
                                            className={
                                                selected === type
                                                    ? "text-[#1a56db]"
                                                    : "text-[#0a0a0a]"
                                            }
                                        />
                                        <CardTitle className="text-xl mt-3">
                                            {title}
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <CardDescription className="text-sm leading-relaxed">
                                            {description}
                                        </CardDescription>
                                    </CardContent>
                                </Card>
                            ),
                        )}
                    </div>

                    <Button
                        size="lg"
                        disabled={!selected}
                        onClick={handleContinue}
                        className="bg-[#1a56db] hover:bg-[#1a56db]/90 text-white px-8 py-3 text-base cursor-pointer"
                    >
                        Continue
                    </Button>
                </div>
            </div>
        </>
    );
}
