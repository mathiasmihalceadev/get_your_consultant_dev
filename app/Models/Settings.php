<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['key', 'value'];

    public static function defaults(): array
    {
        return [
            'auto_send' => false,
            'pricing_rental_living_eur' => '17.99',
            'pricing_buying_living_eur' => '27.99',
            'pricing_exchange_rate_eur_ron' => '5.00',
            'stripe_product_rental_living' => (string) config('services.stripe.products.rental_living', ''),
            'stripe_product_buying_living' => (string) config('services.stripe.products.buying_living', ''),
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $default = array_key_exists($key, static::defaults())
            ? static::defaults()[$key]
            : $default;

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
        $settings = array_merge(
            static::defaults(),
            static::pluck('value', 'key')->toArray(),
        );

        if (isset($settings['auto_send'])) {
            $settings['auto_send'] = (bool) $settings['auto_send'];
        }

        return $settings;
    }
}
