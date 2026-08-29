<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfInstance;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * Generate DomPDF instance for the invoice in given locale.
     */
    public function generatePdf(PurchaseRequest $purchaseRequest, string $locale = 'es'): DomPdfInstance
    {
        $locale = in_array(strtolower($locale), ['es', 'en'], true) ? strtolower($locale) : 'es';
        $request = $purchaseRequest->load(['customer', 'costItems']);

        $companyName = Setting::get('company_name', 'Speed Shopper');
        $warehouseAddress = Setting::get('warehouse_address', 'Miami, FL');
        $whatsappPhone = Setting::get('whatsapp_phone', '+1 (555) 000-0000');
        $companyEmail = Setting::get('company_email', config('mail.from.address'));
        $logoPath = Setting::get('logo_path');

        $logoBase64 = null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoContent = Storage::disk('public')->get($logoPath);
            $mime = Storage::disk('public')->mimeType($logoPath) ?? 'image/png';
            $logoBase64 = 'data:'.$mime.';base64,'.base64_encode($logoContent);
        }

        $cleanWa = preg_replace('/[^0-9]/', '', (string) $whatsappPhone);
        $qrData = $cleanWa ? "https://wa.me/{$cleanWa}?text=".urlencode("Hola Speed Shopper, consulta sobre mi Factura #{$request->number}") : url('/');
        $qrDataUri = app(QrCodeService::class)->generateDataUri($qrData, 70);

        $paymentImageBase64 = null;
        $paymentImgPath = public_path('images/Imagen.png');
        if (! file_exists($paymentImgPath)) {
            $paymentImgPath = public_path('Imagen.png');
        }
        if (file_exists($paymentImgPath)) {
            $imgData = file_get_contents($paymentImgPath);
            $mime = mime_content_type($paymentImgPath) ?: 'image/png';
            $paymentImageBase64 = 'data:'.$mime.';base64,'.base64_encode($imgData);
        }

        $totalCost = (float) $request->total_cost;
        $paidAmount = (float) Payment::where('billable_type', PurchaseRequest::class)
            ->where('billable_id', $request->id)
            ->sum('amount_paid');
        $balance = max(0.0, $totalCost - $paidAmount);

        $pdf = Pdf::loadView('pdf.invoice-pdf', [
            'request' => $request,
            'locale' => $locale,
            'companyName' => $companyName,
            'companyEmail' => $companyEmail,
            'warehouseAddress' => $warehouseAddress,
            'whatsappPhone' => $whatsappPhone,
            'logoBase64' => $logoBase64,
            'qrDataUri' => $qrDataUri,
            'paymentImageBase64' => $paymentImageBase64,
            'totalCost' => $totalCost,
            'paidAmount' => $paidAmount,
            'balance' => $balance,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }
}
