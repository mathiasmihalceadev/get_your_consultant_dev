<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AffiliateTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'notes',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(ReportPurchase::class);
    }

    public static function normalizeSlug(string $value): string
    {
        return Str::of($value)
            ->trim()
            ->lower()
            ->replace('_', '-')
            ->slug('-')
            ->toString();
    }
}
