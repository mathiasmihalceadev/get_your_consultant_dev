<?php

namespace App\Services;

use App\Models\AffiliateTag;
use Illuminate\Http\Request;

class AffiliateAttributionService
{
    public const COOKIE_NAME = 'gyc_affiliate_ref';
    public const COOKIE_DAYS = 10;

    public function tagFromRequest(Request $request): ?AffiliateTag
    {
        $slug = $this->slugFromRequest($request);

        if ($slug === null) {
            return null;
        }

        return AffiliateTag::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function attributesFromRequest(Request $request): array
    {
        $tag = $this->tagFromRequest($request);

        if (!$tag) {
            return [
                'affiliate_tag_id' => null,
                'affiliate_ref' => null,
            ];
        }

        return [
            'affiliate_tag_id' => $tag->id,
            'affiliate_ref' => $tag->slug,
        ];
    }

    public function markUsed(?int $affiliateTagId): void
    {
        if (!$affiliateTagId) {
            return;
        }

        AffiliateTag::query()
            ->whereKey($affiliateTagId)
            ->update(['last_used_at' => now()]);
    }

    public function slugFromRequest(Request $request): ?string
    {
        $ref = $request->query('ref');

        if (is_string($ref) && trim($ref) !== '') {
            return AffiliateTag::normalizeSlug($ref);
        }

        $cookieRef = $request->cookie(self::COOKIE_NAME);

        if (is_string($cookieRef) && trim($cookieRef) !== '') {
            return AffiliateTag::normalizeSlug($cookieRef);
        }

        return null;
    }
}
