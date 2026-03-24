import { usePage } from "@inertiajs/react";
import { PageProps } from "@/types";

export function useTranslation() {
    const { locale, translations } = usePage<PageProps>().props;

    const t = (key: string): string => {
        return translations[key] || key;
    };

    const localePath = (path: string): string => {
        const cleanPath = path.startsWith("/") ? path : `/${path}`;
        return `/${locale}${cleanPath}`;
    };

    return { t, locale: locale as string, localePath };
}
