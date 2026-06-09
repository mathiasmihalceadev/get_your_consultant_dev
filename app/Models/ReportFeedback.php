<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportFeedback extends Model
{
    protected $table = 'report_feedback';

    protected $fillable = [
        'report_id',
        'rating',
        'most_useful_info',
        'wanted_extra',
        'would_recommend',
        'trust_improvement',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'would_recommend' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
