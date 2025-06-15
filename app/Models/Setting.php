<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * Cache duration (minutes)
     */
    const CACHE_DURATION = 1440; // 24 hours

    /**
     * Get a setting value by key with caching
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember(
            "setting_{$key}",
            self::CACHE_DURATION,
            fn() => self::where('key', $key)->value('value') ?? $default
        );
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting_{$key}");
    }

    /**
     * Get all settings as an associative array
     */
    public static function getAll(): array
    {
        return Cache::remember(
            'all_settings',
            self::CACHE_DURATION,
            fn() => self::pluck('value', 'key')->toArray()
        );
    }

    /**
     * Get a boolean setting value
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        return filter_var(self::getValue($key, $default), FILTER_VALIDATE_BOOL);
    }

    /**
     * Get a JSON/array setting value
     */
    public static function getJson(string $key, array $default = []): array
    {
        $value = self::getValue($key);
        return $value ? json_decode($value, true) : $default;
    }

    /**
     * Get a float setting value
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        return (float) self::getValue($key, $default);
    }
}
