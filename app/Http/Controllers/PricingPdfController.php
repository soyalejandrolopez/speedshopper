<?php

namespace App\Http\Controllers;

use App\Services\PricingRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PricingPdfController extends Controller
{
    public function download(Request $request, PricingRateService $service): Response
    {
        $locale = $request->query('lang', 'es');
        $inline = $request->boolean('inline', false);

        $pdf = $service->generatePdf($locale);
        $filename = $locale === 'es' ? 'SpeedShopper_Tarifas.pdf' : 'SpeedShopper_Rate_Sheet.pdf';

        if ($inline) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
