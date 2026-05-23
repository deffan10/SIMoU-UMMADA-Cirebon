<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null): ?string
    {
        return Cache::remember("site_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting_{$key}");
    }

    /**
     * Get logo URL
     */
    public static function logoUrl(): string
    {
        $logo = static::get('site_logo');
        return $logo ? asset('storage/' . $logo) : '';
    }

    /**
     * Get favicon URL
     */
    public static function faviconUrl(): string
    {
        $favicon = static::get('site_favicon');
        return $favicon ? asset('storage/' . $favicon) : asset('favicon.ico');
    }

    /**
     * Check if custom logo exists
     */
    public static function hasLogo(): bool
    {
        return !empty(static::get('site_logo'));
    }
}
