<?php

namespace App\Livewire\Portal\Requests;

use App\Concerns\SwalNotifies;
use App\Enums\CostType;
use App\Models\CostItem;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use App\Services\AdminNotifier;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Nueva Solicitud de Compra')]
class CreateRequest extends Component
{
    use SwalNotifies;

    public array $form = [
        'product_name' => '',
        'product_url' => '',
        'store' => '',
        'size_color' => '',
        'quantity' => 1,
        'unit_price' => '',
        'services' => ['personal_shopper'],
        'has_links' => 'no',
        'product_links' => '',
        'find_deals' => 'no',
        'already_purchased' => 'no',
        'store_name' => '',
        'order_number' => '',
        'tracking_number' => '',
        'boxes_small' => 0,
        'boxes_medium' => 0,
        'boxes_large' => 0,
        'courier' => 'no',
        'courier_name' => '',
        'need_shipping_coordination' => 'no',
        'comments' => '',
    ];

    public function getRatesProperty(): array
    {
        return app(PricingRateService::class)->getRates();
    }

    public function getPackagingTotalProperty(): float
    {
        $rates = $this->rates;
        $smallRate = (float) ($rates['box_small_heavy_duty'] ?? 15.0);
        $mediumRate = (float) ($rates['box_medium_heavy_duty'] ?? 20.0);
        $largeRate = (float) ($rates['box_large_heavy_duty'] ?? 25.0);

        $small = max(0, (int) ($this->form['boxes_small'] ?? 0));
        $med = max(0, (int) ($this->form['boxes_medium'] ?? 0));
        $large = max(0, (int) ($this->form['boxes_large'] ?? 0));

        return ($small * $smallRate) + ($med * $mediumRate) + ($large * $largeRate);
    }

    public function incrementBox(string $type): void
    {
        $key = 'boxes_'.$type;
        if (array_key_exists($key, $this->form)) {
            $this->form[$key] = ((int) ($this->form[$key] ?? 0)) + 1;
        }
    }

    public function decrementBox(string $type): void
    {
        $key = 'boxes_'.$type;
        if (array_key_exists($key, $this->form)) {
            $this->form[$key] = max(0, ((int) ($this->form[$key] ?? 0)) - 1);
        }
    }

    protected function rules(): array
    {
        return [
            'form.product_name' => ['required', 'string', 'max:255'],
            'form.product_url' => ['nullable', 'string', 'max:2000'],
            'form.store' => ['nullable', 'string', 'max:255'],
            'form.size_color' => ['nullable', 'string', 'max:255'],
            'form.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'form.unit_price' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'form.services' => ['required', 'array', 'min:1'],
            'form.services.*' => ['string'],
            'form.has_links' => ['nullable', 'in:yes,no'],
            'form.product_links' => ['nullable', 'string', 'max:2000'],
            'form.find_deals' => ['nullable', 'in:yes,no'],
            'form.already_purchased' => ['nullable', 'in:yes,no'],
            'form.store_name' => ['nullable', 'string', 'max:255'],
            'form.order_number' => ['nullable', 'string', 'max:255'],
            'form.tracking_number' => ['nullable', 'string', 'max:255'],
            'form.boxes_small' => ['nullable', 'integer', 'min:0', 'max:99'],
            'form.boxes_medium' => ['nullable', 'integer', 'min:0', 'max:99'],
            'form.boxes_large' => ['nullable', 'integer', 'min:0', 'max:99'],
            'form.courier' => ['nullable', 'in:yes,no'],
            'form.courier_name' => ['nullable', 'string', 'max:255'],
            'form.need_shipping_coordination' => ['nullable', 'in:yes,no'],
            'form.comments' => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();
        $customer = $user->customer;

        if (! $customer) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? $user->whatsapp,
                'whatsapp' => $user->whatsapp ?? $user->phone,
                'country' => $user->country ?? 'VE',
                'registered_at' => today(),
            ]);
        }

        $smallCount = max(0, (int) ($this->form['boxes_small'] ?? 0));
        $mediumCount = max(0, (int) ($this->form['boxes_medium'] ?? 0));
        $largeCount = max(0, (int) ($this->form['boxes_large'] ?? 0));

        $services = $this->form['services'];
        if (($smallCount > 0 || $mediumCount > 0 || $largeCount > 0) && ! in_array('repack', $services, true) && ! in_array('packing', $services, true)) {
            $services[] = 'repack';
            $services[] = 'packing';
        }

        $unitPrice = ! empty($this->form['unit_price']) ? (float) $this->form['unit_price'] : null;

        $request = $customer->purchaseRequests()->create([
            'product_name' => $this->form['product_name'],
            'product_url' => $this->form['product_url'] ?: null,
            'store' => $this->form['store'] ?: null,
            'size_color' => $this->form['size_color'] ?: null,
            'services' => $services,
            'description' => $this->buildDescription(),
            'status' => 'new',
            'quantity' => (int) ($this->form['quantity'] ?: 1),
            'unit_price' => $unitPrice,
        ]);

        $rates = $this->rates;
        $smallRate = (float) ($rates['box_small_heavy_duty'] ?? 15.0);
        $mediumRate = (float) ($rates['box_medium_heavy_duty'] ?? 20.0);
        $largeRate = (float) ($rates['box_large_heavy_duty'] ?? 25.0);
        $deliveryRate = (float) ($rates['warehouse_delivery_fee'] ?? 20.0);

        // 1. Personal Shopper Cost Items
        if (in_array('personal_shopper', $services, true) || empty($services)) {
            if ($unitPrice !== null && $unitPrice > 0) {
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ProductCost,
                    'description' => 'Valor del Producto',
                    'amount' => $unitPrice,
                ]);

                $percent = $unitPrice >= 700 ? 15.0 : 20.0;
                $commAmount = round($unitPrice * ($percent / 100), 2);
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ShopperFee,
                    'description' => "Comisión Personal Shopper ({$percent}%)",
                    'amount' => $commAmount,
                ]);
            }
        }

        // 2. Comprar Online Cost Items
        if (in_array('online_shopping', $services, true)) {
            if ($unitPrice !== null && $unitPrice > 0) {
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ProductCost,
                    'description' => 'Valor Pagado en Internet: $'.number_format($unitPrice, 2).' (No se cobra en factura)',
                    'amount' => $unitPrice,
                ]);

                $onlinePercent = (float) ($rates['warehouse_percent'] ?? 15.0);
                $commAmount = round($unitPrice * ($onlinePercent / 100), 2);
                CostItem::create([
                    'costable_type' => PurchaseRequest::class,
                    'costable_id' => $request->id,
                    'type' => CostType::ReceivingFee,
                    'description' => "Comisión Almacén / Compras Online ({$onlinePercent}% sobre $".number_format($unitPrice, 2).')',
                    'amount' => $commAmount,
                ]);
            }

            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::ReceivingFee,
                'description' => 'Servicio de Traslado de Caja al Almacén (Fijo $'.number_format($deliveryRate, 2).')',
                'amount' => $deliveryRate,
            ]);
        }

        // 3. Reempaque Cost Items
        if (in_array('repack', $services, true) && ! in_array('online_shopping', $services, true)) {
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::ReceivingFee,
                'description' => 'Servicio de Traslado de Caja al Almacén (Fijo $'.number_format($deliveryRate, 2).')',
                'amount' => $deliveryRate,
            ]);
        }

        // Cajas
        if ($smallCount > 0) {
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::PackingFee,
                'description' => "{$smallCount}x Caja Small Heavy Duty ($".number_format($smallRate, 2).')',
                'amount' => $smallCount * $smallRate,
            ]);
        }

        if ($mediumCount > 0) {
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::PackingFee,
                'description' => "{$mediumCount}x Caja Mediana Heavy Duty ($".number_format($mediumRate, 2).')',
                'amount' => $mediumCount * $mediumRate,
            ]);
        }

        if ($largeCount > 0) {
            CostItem::create([
                'costable_type' => PurchaseRequest::class,
                'costable_id' => $request->id,
                'type' => CostType::PackingFee,
                'description' => "{$largeCount}x Caja Larga Heavy Duty ($".number_format($largeRate, 2).')',
                'amount' => $largeCount * $largeRate,
            ]);
        }

        $package = null;
        if ($this->form['already_purchased'] === 'yes') {
            $package = $customer->packages()->create([
                'purchase_request_id' => $request->id,
                'store' => $this->form['store_name'] ?: $this->form['store'] ?: null,
                'original_tracking' => $this->form['tracking_number'] ?: null,
                'status' => 'received',
            ]);
        }

        $needsShipment = in_array('delivery_to_courier', $this->form['services'], true)
            || $this->form['courier'] === 'yes'
            || $this->form['need_shipping_coordination'] === 'yes';

        if ($needsShipment) {
            $shipment = $customer->shipments()->create([
                'carrier' => $this->form['courier_name'] ?: null,
                'destination_country' => $customer->country ?: 'VE',
                'status' => 'draft',
            ]);

            if ($package) {
                $shipment->packages()->attach($package->id);
            }
        }

        AdminNotifier::notifyNewPurchaseRequest($request);

        session()->flash('swal_title', __('Solicitud enviada exitosamente'));
        session()->flash('swal_text', __('Tu solicitud #:number ha sido registrada y está siendo procesada.', ['number' => $request->number]));

        return $this->redirect(route('portal.requests.show', $request), navigate: true);
    }

    protected function buildDescription(): string
    {
        $f = $this->form;
        $lines = [];

        if ($f['size_color']) {
            $lines[] = __('Talla / Color / Detalles').': '.$f['size_color'];
        }

        if ($f['product_url']) {
            $lines[] = __('Enlace Principal').': '.$f['product_url'];
        }

        if ($f['has_links'] === 'yes' && $f['product_links']) {
            $lines[] = __('Enlaces Adicionales').': '.$f['product_links'];
        }

        if ($f['find_deals'] === 'yes') {
            $lines[] = __('Buscar mejores ofertas / cupones').': '.__('Sí');
        }

        $smallCount = max(0, (int) ($f['boxes_small'] ?? 0));
        $mediumCount = max(0, (int) ($f['boxes_medium'] ?? 0));
        $largeCount = max(0, (int) ($f['boxes_large'] ?? 0));
        if ($smallCount > 0 || $mediumCount > 0 || $largeCount > 0) {
            $boxParts = [];
            if ($smallCount > 0) {
                $boxParts[] = "{$smallCount}x Small";
            }
            if ($mediumCount > 0) {
                $boxParts[] = "{$mediumCount}x Mediana";
            }
            if ($largeCount > 0) {
                $boxParts[] = "{$largeCount}x Larga";
            }
            $lines[] = __('Embalaje / Cajas').': '.implode(', ', $boxParts).' ('.money($this->packagingTotal).')';
        }

        if ($f['already_purchased'] === 'yes') {
            $lines[] = __('Comprado por mi cuenta').': '.__('Sí');
            if ($f['store_name']) {
                $lines[] = __('Tienda').': '.$f['store_name'];
            }
            if ($f['order_number']) {
                $lines[] = __('N° Orden').': '.$f['order_number'];
            }
            if ($f['tracking_number']) {
                $lines[] = __('Tracking').': '.$f['tracking_number'];
            }
        }

        if ($f['courier'] === 'yes' && $f['courier_name']) {
            $lines[] = __('Empresa de Envío Preferida').': '.$f['courier_name'];
        }

        if ($f['need_shipping_coordination'] === 'yes') {
            $lines[] = __('Coordinar Envío Internacional').': '.__('Sí');
        }

        if ($f['comments']) {
            $lines[] = __('Comentarios').': '.$f['comments'];
        }

        return implode("\n", $lines);
    }

    public function render()
    {
        return view('livewire.portal.requests.create-request', [
            'serviceDefinitions' => service_definitions(),
            'serviceOptions' => service_options(),
        ]);
    }
}
