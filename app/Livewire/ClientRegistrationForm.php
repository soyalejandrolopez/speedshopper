<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class ClientRegistrationForm extends Component
{
    public int $step = 1;

    public bool $sent = false;

    public array $form = [
        'name' => '',
        'whatsapp' => '',
        'email' => '',
        'country' => '',
        'city' => '',
        'address' => '',
        'services' => [],
        'products' => '',
        'preferred_stores' => '',
        'budget' => '',
        'has_links' => 'no',
        'product_links' => '',
        'find_deals' => 'no',
        'already_purchased' => 'no',
        'store_name' => '',
        'order_number' => '',
        'tracking_number' => '',
        'approx_packages' => '',
        'courier' => 'no',
        'courier_name' => '',
        'need_shipping_coordination' => 'no',
        'comments' => '',
        'confirm_correct' => false,
        'accept_costs' => false,
        'accept_contact' => false,
    ];

    public const TOTAL_STEPS = 3;

    /** @return array<int, string> */
    public function serviceOptions(): array
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

    /** @return array<string, string> */
    public function countries(): array
    {
        $codes = explode(',', \App\Models\Setting::get('countries_served', 'MX,GT,HN,SV,NI,CR,PA,CO,EC,PE,CL,AR'));

        return collect($codes)->mapWithKeys(fn ($code) => [trim($code) => country_name(trim($code))])->all();
    }

    public function next(): void
    {
        $this->validateStep($this->step);

        if ($this->step < self::TOTAL_STEPS) {
            $this->step++;
        }
    }

    public function back(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit(): void
    {
        $this->validate($this->rules());

        $customer = Customer::firstOrCreate(
            ['email' => $this->form['email']],
            [
                'name' => $this->form['name'],
                'whatsapp' => $this->form['whatsapp'] ?: null,
                'country' => $this->form['country'] ?: null,
                'city' => $this->form['city'] ?: null,
                'address' => $this->form['address'] ?: null,
                'registered_at' => today(),
            ]
        );

        $customer->purchaseRequests()->create([
            'product_name' => mb_substr($this->form['products'] ?: __('Service request'), 0, 255),
            'store' => $this->form['preferred_stores'] ?: null,
            'description' => $this->buildDescription(),
            'status' => 'new',
        ]);

        $this->sent = true;
    }

    public function resetForm(): void
    {
        $this->reset();
    }

    public function progressPercent(): int
    {
        if ($this->sent) {
            return 100;
        }

        return (int) round((($this->step - 1) / self::TOTAL_STEPS) * 100);
    }

    public function validateStep(int $step): void
    {
        $fields = match ($step) {
            1 => ['form.name', 'form.whatsapp', 'form.email', 'form.country', 'form.city', 'form.address', 'form.services'],
            2 => [
                'form.products', 'form.preferred_stores', 'form.budget', 'form.has_links',
                'form.product_links', 'form.find_deals', 'form.already_purchased', 'form.store_name',
                'form.order_number', 'form.tracking_number', 'form.approx_packages',
            ],
            default => [
                'form.courier', 'form.courier_name', 'form.need_shipping_coordination',
                'form.comments', 'form.confirm_correct', 'form.accept_costs', 'form.accept_contact',
            ],
        };

        $rules = array_intersect_key($this->rules(), array_flip($fields));

        $this->validate($rules);
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.whatsapp' => ['required', 'string', 'max:50'],
            'form.email' => ['required', 'email', 'max:255'],
            'form.country' => ['required', 'string', 'max:2'],
            'form.city' => ['nullable', 'string', 'max:255'],
            'form.address' => ['nullable', 'string', 'max:500'],
            'form.services' => ['required', 'array', 'min:1'],
            'form.services.*' => ['string'],
            'form.products' => ['required', 'string', 'max:2000'],
            'form.preferred_stores' => ['nullable', 'string', 'max:255'],
            'form.budget' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'form.has_links' => ['required', 'in:yes,no'],
            'form.product_links' => ['nullable', 'string', 'max:2000'],
            'form.find_deals' => ['required', 'in:yes,no'],
            'form.already_purchased' => ['nullable', 'in:yes,no'],
            'form.store_name' => ['nullable', 'string', 'max:255'],
            'form.order_number' => ['nullable', 'string', 'max:255'],
            'form.tracking_number' => ['nullable', 'string', 'max:255'],
            'form.approx_packages' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'form.courier' => ['nullable', 'in:yes,no'],
            'form.courier_name' => ['nullable', 'string', 'max:255'],
            'form.need_shipping_coordination' => ['nullable', 'in:yes,no'],
            'form.comments' => ['nullable', 'string', 'max:3000'],
            'form.confirm_correct' => ['accepted'],
            'form.accept_costs' => ['accepted'],
            'form.accept_contact' => ['accepted'],
        ];
    }

    protected function buildDescription(): string
    {
        $f = $this->form;
        $options = $this->serviceOptions();
        $lines = [];

        $lines[] = __('Services').': '.(count($f['services'])
            ? implode(', ', array_map(fn ($s) => $options[$s] ?? $s, $f['services']))
            : '—');
        $lines[] = __('Products').': '.($f['products'] ?: '—');
        $lines[] = __('Preferred stores').': '.($f['preferred_stores'] ?: '—');
        $lines[] = __('Budget').': '.($f['budget'] !== '' ? money((float) $f['budget']) : '—');
        $lines[] = __('Has product links').': '.($f['has_links'] === 'yes' ? __('Yes') : __('No'));
        if ($f['has_links'] === 'yes' && $f['product_links']) {
            $lines[] = __('Links').': '.$f['product_links'];
        }
        $lines[] = __('Find deals').': '.($f['find_deals'] === 'yes' ? __('Yes') : __('No'));

        if ($f['already_purchased'] === 'yes') {
            $lines[] = __('Already purchased').': '.__('Yes');
            $lines[] = __('Store').': '.($f['store_name'] ?: '—');
            $lines[] = __('Order number').': '.($f['order_number'] ?: '—');
            $lines[] = __('Tracking').': '.($f['tracking_number'] ?: '—');
            $lines[] = __('Approx packages').': '.($f['approx_packages'] ?: '—');
        } else {
            $lines[] = __('Already purchased').': '.__('No');
        }

        $lines[] = __('Preferred courier').': '.($f['courier'] === 'yes' ? ($f['courier_name'] ?: __('Yes')) : __('No'));
        $lines[] = __('Coordinate shipping').': '.($f['need_shipping_coordination'] === 'yes' ? __('Yes') : __('No'));

        if ($f['comments']) {
            $lines[] = __('Comments').': '.$f['comments'];
        }

        return implode("\n", $lines);
    }

    public function render()
    {
        return view('livewire.client-registration-form');
    }
}
