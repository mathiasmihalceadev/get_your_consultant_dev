@props([
    'events' => [],
])

@php
    $encodedEvents = json_encode($events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
@endphp

@if (!empty($events))
    <script id="blade-data-layer-events" type="application/json">{!! $encodedEvents !!}</script>
    <script>
        window.dataLayer = window.dataLayer || [];
        (function () {
            var eventsElement = document.getElementById('blade-data-layer-events');
            var queuedEvents = [];

            if (eventsElement && eventsElement.textContent) {
                try {
                    queuedEvents = JSON.parse(eventsElement.textContent);
                } catch {
                    queuedEvents = [];
                }
            }

            queuedEvents.forEach(function (eventPayload) {
                eventPayload.page_path = window.location.pathname + window.location.search;
                eventPayload.page_location = window.location.href;
                eventPayload.page_title = document.title;
                window.dataLayer.push(eventPayload);
            });
        })();
    </script>
@endif