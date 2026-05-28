import { PageProps as InertiaPageProps } from "@inertiajs/core";
import { PageProps } from "./";

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps {
        auth: {
            user: import("./").User | null;
        };
        appFlags: import("./").AppFlags;
        flash: import("./").FlashMessages;
    }
}

declare global {
    function route(
        name: string,
        params?: Record<string, unknown> | string | number,
        absolute?: boolean,
    ): string;
    function route(): { current: (name: string) => boolean };
}
