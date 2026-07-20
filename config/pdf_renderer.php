<?php

return [
    'service_url' => env(
        'PDF_RENDERER_SERVICE_URL',
        'https://pdf-render.getyourconsultant.com/internal/render-pdf',
    ),
    'shared_secret' => env(
        'PDF_RENDERER_SHARED_SECRET',
        '9acd6e4de9b8385e34aba982f6789209d52daf3810c53a0e4888a85b813e1f48',
    ),
    'timeout' => (int) env('PDF_RENDERER_TIMEOUT', 180),
];
