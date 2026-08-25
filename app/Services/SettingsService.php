<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected const CACHE_KEY = 'app_settings_cache';

    protected const CACHE_TTL = 86400; // 24 hours

    /**
     * Get all cached settings.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a setting by key with optional default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    /**
     * Get public settings for sharing to frontend / Inertia.
     */
    public function publicSettings(): array
    {
        return Cache::remember('app_public_settings_cache', self::CACHE_TTL, function () {
            return Setting::where('is_public', true)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Set / update a setting.
     */
    public function set(string $key, mixed $value, string $group = 'general', bool $isPublic = true): Setting
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'group' => $group,
                'is_public' => $isPublic,
            ]
        );

        $this->forgetCache();

        return $setting;
    }

    /**
     * Set multiple settings at once.
     */
    public function setMany(array $settings, string $group = 'general'): void
    {
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_array($value) ? json_encode($value) : (string) $value,
                    'group' => $group,
                    'is_public' => true,
                ]
            );
        }

        $this->forgetCache();
    }

    /**
     * Invalidate cached settings.
     */
    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('app_public_settings_cache');
    }
}
