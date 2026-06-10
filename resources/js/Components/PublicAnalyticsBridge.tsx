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

function hasEmittedEventInSession(eventKey: string) {
    if (typeof window === "undefined") {
        return false;
    }

    try {
        const storedEventIds = window.sessionStorage.getItem(
            "gyc_gtm_emitted_events",
        );

        if (!storedEventIds) {
            return false;
        }

        const parsedEventIds = JSON.parse(storedEventIds);

        return (
            Array.isArray(parsedEventIds) && parsedEventIds.includes(eventKey)
        );
    } catch {
        return false;
    }
}

function markEventAsEmittedInSession(eventKey: string) {
    if (typeof window === "undefined") {
        return;
    }

    try {
        const storedEventIds = window.sessionStorage.getItem(
            "gyc_gtm_emitted_events",
        );
        const parsedEventIds = storedEventIds ? JSON.parse(storedEventIds) : [];
        const nextEventIds = Array.isArray(parsedEventIds)
            ? parsedEventIds
            : [];

        if (!nextEventIds.includes(eventKey)) {
            nextEventIds.push(eventKey);
        }

        window.sessionStorage.setItem(
            "gyc_gtm_emitted_events",
            JSON.stringify(nextEventIds),
        );
    } catch {
        // Ignore storage failures and fall back to the in-memory guard.
    }
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
            isAdminPage ||
            flashEvents.length === 0 ||
            typeof window === "undefined"
        ) {
            return;
        }

        flashEvents.forEach((event: DataLayerEvent, index: number) => {
            const eventKey =
                typeof event.event_id === "string" && event.event_id.length > 0
                    ? event.event_id
                    : `${event.event}-${index}-${pagePath}`;

            if (
                emittedEventIds.current.has(eventKey) ||
                hasEmittedEventInSession(eventKey)
            ) {
                return;
            }

            emittedEventIds.current.add(eventKey);
            markEventAsEmittedInSession(eventKey);

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
