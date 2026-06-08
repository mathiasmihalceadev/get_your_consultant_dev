import { useEffect, useMemo, useRef } from "react";
import { usePage } from "@inertiajs/react";

import { DataLayerEvent, PageProps } from "@/types";

function pushDataLayerEvent(event: Record<string, unknown>) {
    if (typeof window === "undefined") {
        return;
    }

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(event);
}

export default function PublicAnalyticsBridge() {
    const page = usePage<PageProps>();
    const emittedEventIds = useRef(new Set<string>());
    const pagePath = page.url;
    const isAdminPage = page.component.startsWith("Admin/");
    const flashEvents = useMemo(
        () => page.props.flash?.dataLayerEvents ?? [],
        [page.props.flash?.dataLayerEvents],
    );

    useEffect(() => {
        if (isAdminPage || typeof window === "undefined") {
            return;
        }

        pushDataLayerEvent({
            event: "spa_page_view",
            page_path: pagePath,
            page_location: window.location.href,
            page_title: document.title,
        });
    }, [isAdminPage, page.component, pagePath]);

    useEffect(() => {
        if (
            isAdminPage
            || flashEvents.length === 0
            || typeof window === "undefined"
        ) {
            return;
        }

        flashEvents.forEach((event: DataLayerEvent, index: number) => {
            const eventKey =
                typeof event.event_id === "string" && event.event_id.length > 0
                    ? event.event_id
                    : `${event.event}-${index}-${pagePath}`;

            if (emittedEventIds.current.has(eventKey)) {
                return;
            }

            emittedEventIds.current.add(eventKey);

            pushDataLayerEvent({
                ...event,
                page_path: window.location.pathname + window.location.search,
                page_location: window.location.href,
                page_title: document.title,
            });
        });
    }, [flashEvents, isAdminPage, pagePath]);

    return null;
}