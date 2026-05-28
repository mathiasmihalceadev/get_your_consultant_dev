<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplySeoIndexingHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!config('seo.indexing')) {
            $response->headers->set('X-Robots-Tag', config('seo.x_robots_tag'));
        }

        return $response;
    }
}