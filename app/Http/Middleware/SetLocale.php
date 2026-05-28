<?php

namespace App\Http\Middleware;

use App\Support\LocalizedUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = LocalizedUrl::requestHost($request);
        $locale = LocalizedUrl::localeForHost($host);

        app()->setLocale($locale);
        $request->attributes->set('active_locale', $locale);

        return $next($request);
    }
}
