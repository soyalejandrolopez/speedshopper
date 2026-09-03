<?php

use App\Models\Setting;
use App\Services\QrCodeService;
use Illuminate\Mail\Message;
use Illuminate\Mail\TextMessage;
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

if (! function_exists('brand_logo_data_uri')) {
    function brand_logo_data_uri(): ?string
    {
        $path = Setting::get('logo_path');
        $disk = Storage::disk('public');

        if (! $path || ! $disk->exists($path)) {
            return null;
        }

        $mime = $disk->mimeType($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($disk->get($path));
    }
}

if (! function_exists('qr_code_svg')) {
    function qr_code_svg(string $data, int $size = 120, string $color = '#1f2937', string $bgColor = '#ffffff'): string
    {
        return app(QrCodeService::class)->generateSvg($data, $size, $color, $bgColor);
    }
}

if (! function_exists('qr_code_data_uri')) {
    function qr_code_data_uri(string $data, int $size = 120, string $color = '#1f2937', string $bgColor = '#ffffff'): string
    {
        return app(QrCodeService::class)->generateDataUri($data, $size, $color, $bgColor);
    }
}

if (! function_exists('mail_logo_cid')) {
    /**
     * Embed the brand logo as an inline (CID) attachment in the given mail message.
     *
     * @param  Message|TextMessage|null  $message
     */
    function mail_logo_cid(mixed $message): ?string
    {
        $path = Setting::get('logo_path');
        $disk = Storage::disk('public');

        if (! $message || ! $path || ! $disk->exists($path)) {
            return null;
        }

        try {
            $cid = $message->embed($disk->path($path));
        } catch (Throwable) {
            return null;
        }

        return $cid ?: null;
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
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            $hex = '670753';
        }

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

        return isset($names[$iso2]) ? __($names[$iso2]) : $iso2;
    }
}

if (! function_exists('countries_served_list')) {
    /**
     * @return array<int, string>
     */
    function countries_served_list(): array
    {
        $setting = Setting::get('countries_served');
        if ($setting) {
            $parsed = array_values(array_filter(array_map('trim', explode(',', strtoupper($setting)))));
            if (! empty($parsed)) {
                return $parsed;
            }
        }

        return ['VE', 'CO', 'EC', 'PE', 'CL', 'CR', 'PA', 'DO', 'SV', 'HN', 'MX'];
    }
}

if (! function_exists('service_definitions')) {
    /**
     * @return array<string, array{key: string, title: string, subtitle: string, icon: string}>
     */
    function service_definitions(): array
    {
        return [
            'personal_shopper' => [
                'key' => 'personal_shopper',
                'title' => __('Personal Shopper'),
                'subtitle' => __('Compras físicas + comisión por tramos (20% - 15%)'),
                'icon' => 'fa-bag-shopping',
            ],
            'online_shopping' => [
                'key' => 'online_shopping',
                'title' => __('Comprar Online'),
                'subtitle' => __('Comisión 15% + traslado fijo $20 (no se cobra el producto)'),
                'icon' => 'fa-globe',
            ],
            'repack' => [
                'key' => 'repack',
                'title' => __('Reempaque'),
                'subtitle' => __('Cajas Small $15, Med $20, Larga $25 + traslado $20'),
                'icon' => 'fa-boxes-packing',
            ],
        ];
    }
}

if (! function_exists('service_options')) {
    /**
     * @return array<string, string>
     */
    function service_options(): array
    {
        return [
            'personal_shopper' => __('Personal Shopper (Compras físicas + comisión 20% - 15%)'),
            'online_shopping' => __('Comprar Online (Comisión 15% + traslado fijo $20)'),
            'repack' => __('Reempaque (Cajas Small $15, Med $20, Larga $25 + traslado $20)'),
            'package_reception' => __('Recepción de Paquetes'),
            'consolidation' => __('Consolidación de Paquetes'),
            'packing' => __('Reempaque y Cajas'),
            'delivery_to_courier' => __('Traslado a Empresa de Envío'),
        ];
    }
}
