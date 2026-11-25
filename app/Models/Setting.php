<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * In-memory cache for all settings to prevent redundant DB queries
     */
    protected static ?array $runtimeCache = null;

    public static function allCached(): array
    {
        if (static::$runtimeCache === null) {
            static::$runtimeCache = static::all()->keyBy('key')->all();
        }
        return static::$runtimeCache;
    }

    public static function clearRuntimeCache(): void
    {
        static::$runtimeCache = null;
    }

    /**
     * Get a setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $settings = static::allCached();
        $setting = $settings[$key] ?? null;

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            'integer' => (int) $setting->value,
            default => $setting->value,
        };
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, $value, string $type = 'string'): void
    {
        $storedValue = $type === 'json' ? json_encode($value) : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'type' => $type]
        );

        static::clearRuntimeCache();
    }

    /**
     * Get default definition for all supported marketplaces/platforms
     */
    public static function getDefaultMarketplaces(): array
    {
        return [
            'tiktok' => [
                'key' => 'tiktok',
                'name' => 'TikTok Live',
                'short_name' => 'TikTok',
                'enabled' => true,
                'default_hourly_rate' => 20000,
                'color' => '#0f172a',
                'bg_badge' => '#0f172a',
                'text_badge' => '#ffffff',
                'badge_bg' => '#0f172a',
                'badge_color' => '#ffffff',
                'icon' => '',
                'link_label' => 'Link Video TikTok (VT)',
                'link_placeholder' => 'https://vt.tiktok.com/...',
            ],
            'shopee' => [
                'key' => 'shopee',
                'name' => 'Shopee Live',
                'short_name' => 'Shopee',
                'enabled' => true,
                'default_hourly_rate' => 20000,
                'color' => '#ea580c',
                'bg_badge' => '#ea580c',
                'text_badge' => '#ffffff',
                'badge_bg' => '#ea580c',
                'badge_color' => '#ffffff',
                'icon' => '',
                'link_label' => 'Link Live Shopee / Bukti Live',
                'link_placeholder' => 'https://live.shopee.co.id/...',
            ],
            'tokopedia' => [
                'key' => 'tokopedia',
                'name' => 'Tokopedia Play',
                'short_name' => 'Tokopedia',
                'enabled' => false,
                'default_hourly_rate' => 20000,
                'color' => '#16a34a',
                'bg_badge' => '#16a34a',
                'text_badge' => '#ffffff',
                'badge_bg' => '#16a34a',
                'badge_color' => '#ffffff',
                'icon' => '',
                'link_label' => 'Link Live Tokopedia Play',
                'link_placeholder' => 'https://tokopedia.link/...',
            ],
            'lazada' => [
                'key' => 'lazada',
                'name' => 'LazLive',
                'short_name' => 'Lazada',
                'enabled' => false,
                'default_hourly_rate' => 20000,
                'color' => '#2563eb',
                'bg_badge' => '#2563eb',
                'text_badge' => '#ffffff',
                'badge_bg' => '#2563eb',
                'badge_color' => '#ffffff',
                'icon' => '',
                'link_label' => 'Link LazLive Stream',
                'link_placeholder' => 'https://s.lazada.co.id/...',
            ],
        ];
    }

    /**
     * Get all marketplaces merged with stored settings
     */
    public static function getMarketplaces(): array
    {
        $defaults = static::getDefaultMarketplaces();
        $stored = static::getValue('marketplaces', []);

        if (empty($stored) || !is_array($stored)) {
            return $defaults;
        }

        foreach ($defaults as $key => $item) {
            if (isset($stored[$key])) {
                $defaults[$key]['enabled'] = (bool) ($stored[$key]['enabled'] ?? false);
                if (isset($stored[$key]['default_hourly_rate'])) {
                    $defaults[$key]['default_hourly_rate'] = (int) $stored[$key]['default_hourly_rate'];
                }
                if (isset($stored[$key]['name']) && !empty($stored[$key]['name'])) {
                    $defaults[$key]['name'] = $stored[$key]['name'];
                }
            }
        }

        return $defaults;
    }

    /**
     * Get only active (enabled) marketplaces
     */
    public static function getActiveMarketplaces(): array
    {
        $all = static::getMarketplaces();
        $active = array_filter($all, fn($item) => !empty($item['enabled']));

        // Fallback: at least TikTok is active if all are disabled
        if (empty($active)) {
            $active['tiktok'] = $all['tiktok'];
            $active['tiktok']['enabled'] = true;
        }

        return $active;
    }

    /**
     * Get details for a specific platform/marketplace
     */
    public static function getPlatformInfo(string $platform): array
    {
        $all = static::getMarketplaces();
        return $all[$platform] ?? [
            'key' => $platform,
            'name' => ucfirst($platform),
            'short_name' => ucfirst($platform),
            'enabled' => true,
            'default_hourly_rate' => 20000,
            'color' => '#64748b',
            'bg_badge' => '#64748b',
            'text_badge' => '#ffffff',
            'badge_bg' => '#64748b',
            'badge_color' => '#ffffff',
            'icon' => '',
            'link_label' => 'Link Video / Live',
            'link_placeholder' => 'https://...',
        ];
    }
}
