<?php

return [
    'indexing' => filter_var(env('SEO_INDEXING', false), FILTER_VALIDATE_BOOL),
    'x_robots_tag' => 'noindex, nofollow, noarchive',
];