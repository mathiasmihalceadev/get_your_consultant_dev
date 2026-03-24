<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        if ($key === 'auto_send') {
            return (bool) $setting->value;
        }

        return $setting->value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : $value]
        );
    }

    public static function getAllSettings(): array
    {
        $settings = static::pluck('value', 'key')->toArray();

        if (isset($settings['auto_send'])) {
            $settings['auto_send'] = (bool) $settings['auto_send'];
        }

        return $settings;
    }
}
