import { Link, usePage, router } from "@inertiajs/react";
import { ChartBar, Gear, SignOut, Icon } from "@phosphor-icons/react";
import { PropsWithChildren } from "react";

interface NavItem {
    name: string;
    href: string;
    icon: Icon;
    routeName: string;
}

const navItems: NavItem[] = [
    {
        name: "Dashboard",
        href: "/admin/dashboard",
        icon: ChartBar,
        routeName: "admin.dashboard",
    },
    {
        name: "Settings",
        href: "/admin/settings",
        icon: Gear,
        routeName: "admin.settings",
    },
];

export default function AdminLayout({ children }: PropsWithChildren) {
    const { auth, flash } = usePage().props;
    const currentUrl = usePage().url;

    const handleLogout = (e: React.MouseEvent) => {
        e.preventDefault();
        router.post("/logout");
    };

    return (
        <div className="flex min-h-screen">
            {/* Sidebar */}
            <aside className="w-64 bg-slate-900 text-white flex flex-col fixed h-full">
                <div className="p-6 border-b border-slate-700">
                    <h1 className="text-lg font-bold">Property Reports</h1>
                    <p className="text-xs text-slate-400 mt-1">Admin Panel</p>
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
                                        ? "bg-slate-700 text-white"
                                        : "text-slate-300 hover:bg-slate-800 hover:text-white"
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

                <div className="p-4 border-t border-slate-700">
                    <div className="text-xs text-slate-400 mb-2">
                        {auth?.user?.email}
                    </div>
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-2 text-sm text-slate-300 hover:text-white transition-colors cursor-pointer"
                    >
                        <SignOut size={16} />
                        Logout
                    </button>
                </div>
            </aside>

            {/* Main content */}
            <main className="flex-1 ml-64 bg-gray-50 min-h-screen">
                {/* Flash messages */}
                {flash?.success && (
                    <div className="mx-6 mt-4 p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-800">
                        {flash.success}
                    </div>
                )}
                {flash?.error && (
                    <div className="mx-6 mt-4 p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-800">
                        {flash.error}
                    </div>
                )}

                <div className="p-6">{children}</div>
            </main>
        </div>
    );
}
