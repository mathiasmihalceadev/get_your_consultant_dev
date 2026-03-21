<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'purchase_prompt',
        'rental_prompt',
        'commercial_prompt',
        'auto_send',
    ];

    protected function casts(): array
    {
        return [
            'auto_send' => 'boolean',
        ];
    }
}
