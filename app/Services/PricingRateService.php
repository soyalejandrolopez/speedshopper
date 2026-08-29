<?php

namespace App\Services;

use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfInstance;
use Illuminate\Support\Facades\Storage;

class PricingRateService
{
    public const SETTING_KEY = 'pricing_rate_sheet';

    /**
     * Get default rates structured with all user-defined values.
     */
    public static function defaultRates(): array
    {
        return [
            // Personal Shopper Tiers
            'shopper_tiers' => [
                [
                    'min' => 100,
                    'max' => 699,
                    'percent' => 20,
                    'stores' => 2,
                    'hours' => 2,
                ],
                [
                    'min' => 700,
                    'max' => 1499,
                    'percent' => 15,
                    'stores' => 3,
                    'hours' => 3,
                ],
                [
                    'min' => 1500,
                    'max' => null, // 1500 y más
                    'percent' => 15,
                    'stores' => 4,
                    'hours' => 4,
                ],
            ],
            'extra_store_fee' => 20.00,

            // Warehouse & Repackaging Fees
            'warehouse_percent' => 15.0,
            'box_small_heavy_duty' => 15.00,
            'box_medium_heavy_duty' => 20.00,
            'box_large_heavy_duty' => 25.00,
            'warehouse_delivery_fee' => 20.00,
            'monthly_storage_fee' => 15.00,

            // Explanatory Notes
            'notes_es' => [
                'repackage_notice' => 'Estos precios son del reempaque si ustedes realizan la compra por cualquier página online y yo recibo aquí en casa.',
                'storage_notice' => 'Si sus cajas permanecen un mes o más en nuestro almacén, tendrá un costo adicional de $15 por mes.',
            ],
            'notes_en' => [
                'repackage_notice' => 'These repackaging rates apply when you make the purchase on any online site and we receive it at our address.',
                'storage_notice' => 'If your boxes remain in our warehouse for one month or longer, an additional fee of $15 per month will apply.',
            ],
        ];
    }

    /**
     * Retrieve current rate sheet settings or fallback to defaults.
     */
    public function getRates(): array
    {
        $raw = Setting::get(self::SETTING_KEY);

        if (! $raw) {
            return self::defaultRates();
        }

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        if (! is_array($decoded)) {
            return self::defaultRates();
        }

        return array_replace_recursive(self::defaultRates(), $decoded);
    }

    /**
     * Save rate sheet data.
     */
    public function saveRates(array $data): void
    {
        Setting::set(self::SETTING_KEY, json_encode($data));
    }

    /**
     * Generate DomPDF instance for the pricing rate sheet in given locale.
     */
    public function generatePdf(string $locale = 'es'): DomPdfInstance
    {
        $locale = in_array(strtolower($locale), ['es', 'en'], true) ? strtolower($locale) : 'es';
        $rates = $this->getRates();

        $companyName = Setting::get('company_name', 'Speed Shopper');
        $warehouseAddress = Setting::get('warehouse_address', '7835 Wood Hollow Dr, Baytown, TX 77521, USA');
        $whatsappPhone = Setting::get('whatsapp_phone', '+1 (555) 000-0000');
        $logoPath = Setting::get('logo_path');

        $logoBase64 = null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoContent = Storage::disk('public')->get($logoPath);
            $mime = Storage::disk('public')->mimeType($logoPath) ?? 'image/png';
            $logoBase64 = 'data:'.$mime.';base64,'.base64_encode($logoContent);
        }

        $cleanWa = preg_replace('/[^0-9]/', '', (string) $whatsappPhone);
        $qrData = $cleanWa ? "https://wa.me/{$cleanWa}?text=".urlencode('Hola Speed Shopper, deseo cotizar un servicio.') : url('/');
        $qrDataUri = app(QrCodeService::class)->generateDataUri($qrData, 70);

        $pdf = Pdf::loadView('pdf.pricing-rates-pdf', [
            'rates' => $rates,
            'locale' => $locale,
            'companyName' => $companyName,
            'warehouseAddress' => $warehouseAddress,
            'whatsappPhone' => $whatsappPhone,
            'logoBase64' => $logoBase64,
            'qrDataUri' => $qrDataUri,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }
}
