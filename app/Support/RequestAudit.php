<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestAudit
{
    public static function fromRequest(Request $request): array
    {
        $userAgent = self::cleanHeader($request->userAgent(), 512);

        return array_filter([
            'ip' => $request->ip(),
            'user_agent' => $userAgent,
            'user_agent_hash' => $userAgent !== null ? substr(hash('sha256', $userAgent), 0, 16) : null,
            'accept_language' => self::cleanHeader($request->header('accept-language'), 255),
            'referer' => self::cleanHeader($request->headers->get('referer'), 512),
            'origin' => self::cleanHeader($request->headers->get('origin'), 255),
            'host' => self::cleanHeader($request->getHost(), 255),
            'method' => $request->method(),
            'path' => '/' . ltrim($request->path(), '/'),
            'route' => $request->route()?->getName(),
        ], static fn (mixed $value): bool => $value !== null && $value !== '');
    }

    private static function cleanHeader(?string $value, int $limit): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $value = preg_replace('/[\x00-\x1F\x7F]+/', ' ', $value) ?? $value;

        return strlen($value) > $limit ? substr($value, 0, $limit) : $value;
    }
}
