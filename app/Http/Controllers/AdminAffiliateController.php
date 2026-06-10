<?php

namespace App\Http\Controllers;

use App\Models\AffiliateTag;
use App\Models\Report;
use App\Models\ReportPurchase;
use App\Support\LocalizedUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminAffiliateController extends Controller
{
    public function index(): Response
    {
        $tags = AffiliateTag::query()
            ->withCount([
                'reports',
                'purchases',
                'purchases as paid_purchases_count' => fn ($query) => $query->where('status', 'paid'),
            ])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $revenueTotals = $this->revenueTotalsFor($tags->getCollection()->pluck('id')->all());

        $tags->getCollection()->transform(function (AffiliateTag $tag) use ($revenueTotals) {
            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'is_active' => $tag->is_active,
                'notes' => $tag->notes,
                'last_used_at' => $tag->last_used_at,
                'created_at' => $tag->created_at,
                'updated_at' => $tag->updated_at,
                'reports_count' => $tag->reports_count,
                'purchases_count' => $tag->purchases_count,
                'paid_purchases_count' => $tag->paid_purchases_count,
                'revenue_totals' => $revenueTotals[$tag->id] ?? [],
            ];
        });

        return Inertia::render('Admin/Affiliates', [
            'tags' => $tags,
            'counts' => [
                'total' => AffiliateTag::count(),
                'active' => AffiliateTag::where('is_active', true)->count(),
                'reports' => Report::whereNotNull('affiliate_tag_id')->count(),
                'paid_purchases' => ReportPurchase::whereNotNull('affiliate_tag_id')
                    ->where('status', 'paid')
                    ->count(),
            ],
            'tracking' => [
                'parameter' => 'ref',
                'cookie_days' => 10,
                'base_urls' => collect(LocalizedUrl::publicLocales())
                    ->mapWithKeys(fn (string $locale) => [
                        $locale => rtrim(LocalizedUrl::publicUrlForLocale($locale, '/'), '/'),
                    ])
                    ->all(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = AffiliateTag::normalizeSlug($validated['slug'] ?: $validated['name']);

        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'max:255', 'unique:affiliate_tags,slug'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        AffiliateTag::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'notes' => $validated['notes'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return back()->with('success', 'Tag-ul de afiliat a fost creat.');
    }

    public function update(Request $request, AffiliateTag $affiliateTag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $slug = AffiliateTag::normalizeSlug($validated['slug']);

        $validator = Validator::make(['slug' => $slug], [
            'slug' => [
                'required',
                'max:255',
                Rule::unique('affiliate_tags', 'slug')->ignore($affiliateTag->id),
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $affiliateTag->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'Tag-ul de afiliat a fost actualizat.');
    }

    private function revenueTotalsFor(array $affiliateTagIds): array
    {
        if ($affiliateTagIds === []) {
            return [];
        }

        return ReportPurchase::query()
            ->selectRaw('affiliate_tag_id, COALESCE(paid_currency, currency) as revenue_currency, SUM(amount_total) as amount_minor')
            ->whereIn('affiliate_tag_id', $affiliateTagIds)
            ->where('status', 'paid')
            ->whereNotNull('amount_total')
            ->groupBy('affiliate_tag_id', 'revenue_currency')
            ->get()
            ->groupBy('affiliate_tag_id')
            ->map(fn ($rows) => $rows
                ->map(fn ($row) => [
                    'currency' => strtoupper((string) $row->revenue_currency),
                    'amount_minor' => (int) $row->amount_minor,
                ])
                ->values()
                ->all())
            ->all();
    }
}
