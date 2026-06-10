<?php

namespace App\Http\Middleware;

use App\Models\AffiliateTag;
use App\Services\AffiliateAttributionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class CaptureAffiliateRef
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $ref = $request->query('ref');

        if (!is_string($ref) || trim($ref) === '') {
            return $response;
        }

        $slug = AffiliateTag::normalizeSlug($ref);

        if ($slug === '') {
            return $response;
        }

        $tagExists = AffiliateTag::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->exists();

        if (!$tagExists) {
            return $response;
        }

        $response->headers->setCookie(
            Cookie::create(
                AffiliateAttributionService::COOKIE_NAME,
                $slug,
                now()->addDays(AffiliateAttributionService::COOKIE_DAYS),
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                Cookie::SAMESITE_LAX,
            ),
        );

        return $response;
    }
}
