import { usePage } from "@inertiajs/react";
import { PageProps } from "@/types";

export function useTranslation() {
    const {
        locale,
        translations,
        domainUrls,
        localizedUrls,
        supportedLocales,
        seoIndexing,
    } = usePage<PageProps>().props;

    const t = (key: string): string => {
        return translations[key] || key;
    };

    const localePath = (path: string): string => {
        if (!path) {
            return "/";
        }

        return path.startsWith("/") ? path : `/${path}`;
    };

    return {
        t,
        locale: locale as string,
        localePath,
        domainUrls,
        localizedUrls,
        supportedLocales,
        seoIndexing,
    };
}
