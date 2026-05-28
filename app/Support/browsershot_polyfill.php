<?php

namespace Spatie\Browsershot;

if (! \function_exists(__NAMESPACE__.'\\escapeshellarg')) {
    function escapeshellarg(string $value): string
    {
        return "'".\str_replace("'", "'\"'\"'", $value)."'";
    }
}