<?php

namespace App\Livewire\Admin\Billing;

use App\Concerns\SwalNotifies;
use App\Mail\PricingRatesMail;
use App\Models\CostItem;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Shipment;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Facturación y Tarifas')]
class BillingIndex extends Component
{
    use SwalNotifies;

    public array $rates = [];

    public bool $showSendModal = false;

    public string $recipientEmail = '';

    public string $emailLocale = 'es';

    public string $customEmailNote = '';

    public function rules(): array
    {
        return [
            'rates.shopper_tiers' => ['required', 'array', 'min:1'],
            'rates.shopper_tiers.*.min' => ['required', 'numeric', 'min:0'],
            'rates.shopper_tiers.*.max' => ['nullable', 'numeric', 'min:0'],
            'rates.shopper_tiers.*.percent' => ['required', 'numeric', 'min:0'],
            'rates.shopper_tiers.*.stores' => ['required', 'integer', 'min:1'],
            'rates.shopper_tiers.*.hours' => ['required', 'integer', 'min:1'],
            'rates.extra_store_fee' => ['required', 'numeric', 'min:0'],
            'rates.warehouse_percent' => ['required', 'numeric', 'min:0'],
            'rates.box_small_heavy_duty' => ['required', 'numeric', 'min:0'],
            'rates.box_medium_heavy_duty' => ['required', 'numeric', 'min:0'],
            'rates.box_large_heavy_duty' => ['required', 'numeric', 'min:0'],
            'rates.warehouse_delivery_fee' => ['required', 'numeric', 'min:0'],
            'rates.monthly_storage_fee' => ['required', 'numeric', 'min:0'],
            'rates.notes_es.repackage_notice' => ['nullable', 'string', 'max:500'],
            'rates.notes_es.storage_notice' => ['nullable', 'string', 'max:500'],
            'rates.notes_en.repackage_notice' => ['nullable', 'string', 'max:500'],
            'rates.notes_en.storage_notice' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function mount(?PricingRateService $rateService = null): void
    {
        $rateService ??= app(PricingRateService::class);
        $this->rates = $rateService->getRates();
    }

    public function save(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $this->validate();

        app(PricingRateService::class)->saveRates($validated['rates']);

        $this->swalUpdated(__('Tarifas y configuración de facturación guardadas correctamente.'));
    }

    public function openSendModal(): void
    {
        $this->resetValidation();
        $this->recipientEmail = '';
        $this->emailLocale = 'es';
        $this->customEmailNote = '';
        $this->showSendModal = true;
    }

    public function closeSendModal(): void
    {
        $this->showSendModal = false;
        $this->resetValidation();
    }

    public function sendRatesEmail(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate([
            'recipientEmail' => ['required', 'email', 'max:255'],
            'emailLocale' => ['required', 'in:es,en'],
            'customEmailNote' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $rateService = app(PricingRateService::class);
            $pdf = $rateService->generatePdf($this->emailLocale);
            $pdfOutput = $pdf->output();
            $filename = $this->emailLocale === 'es' ? 'SpeedShopper_Tarifas.pdf' : 'SpeedShopper_Rate_Sheet.pdf';

            $mailable = new PricingRatesMail(
                locale: $this->emailLocale,
                customMessage: $this->customEmailNote,
                pdfOutput: $pdfOutput,
                pdfFilename: $filename
            );

            $adminEmails = array_filter(array_map('trim', explode(',', Setting::get('admin_notification_email', ''))));

            $mail = Mail::to($this->recipientEmail);

            if (! empty($adminEmails)) {
                $mail->bcc($adminEmails);
            }

            $mail->send($mailable);

            $this->showSendModal = false;
            $this->swalSuccess(
                __('Price List PDF sent successfully to').' '.$this->recipientEmail.(! empty($adminEmails) ? ' '.__('(with copy to admin)') : '')
            );
        } catch (\Throwable $e) {
            Log::error('Sending pricing rates mail failed: '.$e->getMessage());
            $this->swalError(__('Could not send email').': '.$e->getMessage());
        }
    }

    public function render()
    {
        $totalInvoicedRequests = CostItem::where('costable_type', PurchaseRequest::class)
            ->sum('amount');

        $totalInvoicedShipments = Shipment::query()
            ->whereNotNull('shipping_cost')
            ->sum('shipping_cost');

        $recentQuotes = PurchaseRequest::with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.billing.billing-index', [
            'totalInvoicedRequests' => $totalInvoicedRequests,
            'totalInvoicedShipments' => $totalInvoicedShipments,
            'recentQuotes' => $recentQuotes,
        ]);
    }
}
