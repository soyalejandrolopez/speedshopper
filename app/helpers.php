<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('money')) {
    function money(float|int|string|null $amount, string $currency = 'USD'): string
    {
        $amount = (float) $amount;

        return sprintf('$%s', number_format($amount, 2));
    }
}

if (! function_exists('brand_logo_url')) {
    function brand_logo_url(): ?string
    {
        $path = Setting::get('logo_path');

        return $path ? Storage::disk('public')->url($path) : null;
    }
}

if (! function_exists('brand_favicon_url')) {
    function brand_favicon_url(): ?string
    {
        $path = Setting::get('favicon_path');

        return $path ? Storage::disk('public')->url($path) : null;
    }
}

if (! function_exists('theme_color')) {
    function theme_color(): string
    {
        return Setting::get('theme_color', '#059669');
    }
}

if (! function_exists('theme_color_is_default')) {
    function theme_color_is_default(): bool
    {
        return strtolower((string) theme_color()) === '#059669';
    }
}

if (! function_exists('theme_color_ramp')) {
    /**
     * Build a shade ramp (50..950) from a base hex color by mixing with white/black.
     *
     * @return array<string, string>
     */
    function theme_color_ramp(string $hex): array
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // [shade => toward white factor] / [shade => toward black factor]
        $lighter = [
            50 => 0.90, 100 => 0.80, 200 => 0.66, 300 => 0.50, 400 => 0.33,
        ];
        $darker = [
            600 => 0.22, 700 => 0.38, 800 => 0.52, 900 => 0.64, 950 => 0.80,
        ];

        $ramp = ['500' => sprintf('#%02x%02x%02x', $r, $g, $b)];

        foreach ($lighter as $shade => $w) {
            $ramp[$shade] = sprintf(
                '#%02x%02x%02x',
                (int) round($r + (255 - $r) * $w),
                (int) round($g + (255 - $g) * $w),
                (int) round($b + (255 - $b) * $w)
            );
        }

        foreach ($darker as $shade => $k) {
            $ramp[$shade] = sprintf(
                '#%02x%02x%02x',
                (int) round($r * (1 - $k)),
                (int) round($g * (1 - $k)),
                (int) round($b * (1 - $k))
            );
        }

        return $ramp;
    }
}

if (! function_exists('country_name')) {
    function country_name(?string $iso2): string
    {
        if (! $iso2) {
            return '—';
        }

        $names = [
            'AR' => 'Argentina',
            'BO' => 'Bolivia',
            'BR' => 'Brasil',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'DO' => 'República Dominicana',
            'EC' => 'Ecuador',
            'GT' => 'Guatemala',
            'HN' => 'Honduras',
            'MX' => 'México',
            'NI' => 'Nicaragua',
            'PA' => 'Panamá',
            'PE' => 'Perú',
            'PY' => 'Paraguay',
            'SV' => 'El Salvador',
            'US' => 'Estados Unidos',
            'UY' => 'Uruguay',
            'VE' => 'Venezuela',
        ];

        return $names[$iso2] ?? $iso2;
    }
}

if (! function_exists('service_options')) {
    /**
     * @return array<string, string>
     */
    function service_options(): array
    {
        return [
            'personal_shopper' => __('In-store shopping'),
            'online_shopping' => __('Online shopping'),
            'package_reception' => __('Package reception'),
            'consolidation' => __('Package consolidation'),
            'packing' => __('Packing service'),
            'delivery_to_courier' => __('Delivery to shipping company'),
        ];
    }
}
