<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale', 'en');

        if (!in_array($locale, ['en', 'ro'])) {
            abort(404);
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
