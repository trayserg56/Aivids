<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /** @param  array<string, mixed>  $default */
    public static function get(string $key, array $default = []): array
    {
        $record = static::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }

    /** @param  array<string, mixed>  $value */
    public static function set(string $key, array $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget('home.page');
    }
}
