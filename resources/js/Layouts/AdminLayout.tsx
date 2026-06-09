import { Link, usePage, router } from "@inertiajs/react";
import {
    ChartBar,
    ChatCenteredText,
    EnvelopeSimple,
    FilePdf,
    Gear,
    SignOut,
    Icon,
} from "@phosphor-icons/react";
import { PropsWithChildren } from "react";
import { PageProps } from "@/types";
import { cn } from "@/lib/utils";

interface NavItem {
    name: string;
    href: string;
    icon: Icon;
}

const navItems: NavItem[] = [
    {
        name: "Panou de Control",
        href: "/admin/dashboard",
        icon: ChartBar,
    },
    {
        name: "Mesaje",
        href: "/admin/inquiries",
        icon: EnvelopeSimple,
    },
    {
        name: "Feedback",
        href: "/admin/feedbacks",
        icon: ChatCenteredText,
    },
    {
        name: "Setări",
        href: "/admin/settings",
        icon: Gear,
    },
    {
        name: "Teste",
        href: "/admin/tests",
        icon: FilePdf,
    },
];

export default function AdminLayout({ children }: PropsWithChildren) {
    const page = usePage<PageProps>();
    const { auth, flash } = page.props;
    const currentUrl = page.url;

    const handleLogout = (e: React.MouseEvent) => {
        e.preventDefault();
        router.post("/logout");
    };

    return (
        <div className="min-h-screen bg-gray-50 lg:flex">
            <aside className="fixed inset-y-0 hidden h-full w-64 flex-col bg-brand-primary text-white lg:flex">
                <div className="p-6 border-b border-white/10">
                    <h1 className="text-lg font-bold">Get Your Consultant</h1>
                    <p className="text-xs text-white/50 mt-1">Panou Admin</p>
                </div>

                <nav className="flex-1 p-4 space-y-1">
                    {navItems.map(({ name, href, icon: Icon }) => {
                        const isActive = currentUrl.startsWith(href);
                        return (
                            <Link
                                key={href}
                                href={href}
                                className={`flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors ${
                                    isActive
                                        ? "bg-white/15 text-white"
                                        : "text-white/70 hover:bg-white/10 hover:text-white"
                                }`}
                            >
                                <Icon
                                    size={20}
                                    weight={isActive ? "fill" : "regular"}
                                />
                                {name}
                            </Link>
                        );
                    })}
                </nav>

                <div className="p-4 border-t border-white/10">
                    <div className="text-xs text-white/50 mb-2">
                        {auth?.user?.email}
                    </div>
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors cursor-pointer"
                    >
                        <SignOut size={16} />
                        Deconectare
                    </button>
                </div>
            </aside>

            <div className="flex min-h-screen flex-1 flex-col lg:ml-64">
                <header className="sticky top-0 z-30 border-b border-brand-primary/10 bg-white/95 backdrop-blur lg:hidden">
                    <div className="px-4 py-4 sm:px-6">
                        <div className="flex items-center justify-between gap-4">
                            <div>
                                <p className="text-sm font-medium text-brand-primary">
                                    Get Your Consultant
                                </p>
                                {auth?.user?.email && (
                                    <p className="mt-1 text-xs text-brand-neutral">
                                        {auth.user.email}
                                    </p>
                                )}
                            </div>

                            <button
                                onClick={handleLogout}
                                className="inline-flex h-10 w-10 items-center justify-center border border-brand-primary/10 bg-brand-primary/4 text-brand-primary transition-colors hover:bg-brand-primary/10"
                                aria-label="Deconectare"
                            >
                                <SignOut size={18} />
                            </button>
                        </div>
                    </div>
                </header>

                <main className="flex-1 bg-gray-50 pb-24 lg:min-h-screen lg:pb-0">
                    {flash?.success && (
                        <div className="mx-4 mt-4 rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800 sm:mx-6 lg:mx-6 lg:mt-4">
                            {flash.success}
                        </div>
                    )}
                    {flash?.error && (
                        <div className="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800 sm:mx-6 lg:mx-6 lg:mt-4">
                            {flash.error}
                        </div>
                    )}

                    <div className="px-4 py-4 sm:px-6 lg:p-6">{children}</div>
                </main>

                <nav className="fixed inset-x-0 bottom-0 z-30 border-t border-brand-primary/10 bg-white/95 px-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-2 backdrop-blur lg:hidden">
                    <div className="grid grid-cols-5 gap-1">
                        {navItems.map(({ name, href, icon: Icon }) => {
                            const isActive = currentUrl.startsWith(href);

                            return (
                                <Link
                                    key={href}
                                    href={href}
                                    className={cn(
                                        "flex min-h-14 flex-col items-center justify-center gap-1 border px-2 py-2 text-[11px] font-medium transition-colors",
                                        isActive
                                            ? "border-brand-primary bg-brand-primary text-white"
                                            : "border-transparent text-brand-primary/65 hover:bg-brand-primary/6 hover:text-brand-primary",
                                    )}
                                >
                                    <Icon
                                        size={18}
                                        weight={isActive ? "fill" : "regular"}
                                    />
                                    <span>{name}</span>
                                </Link>
                            );
                        })}
                    </div>
                </nav>
            </div>
        </div>
    );
}
