<?php

namespace App\Livewire;

use App\Models\ContactInquiry;
use App\Models\Customer;
use App\Models\Setting;
use App\Services\AdminNotifier;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ContactForm extends Component
{
    public array $form = [
        'name' => '',
        'email' => '',
        'whatsapp' => '',
        'country' => 'VE',
        'subject' => 'general',
        'message' => '',
    ];

    public bool $sent = false;

    public ?string $whatsappUrl = null;

    /** @return array<string, string> */
    public function countries(): array
    {
        $raw = Setting::get('countries_served');
        $codes = $raw ? explode(',', $raw) : ['VE', 'CO', 'EC', 'PE', 'CL', 'CR', 'PA', 'DO', 'SV', 'HN', 'MX'];

        return collect($codes)
            ->map(fn ($code) => strtoupper(trim($code)))
            ->filter()
            ->unique()
            ->reject(fn ($code) => $code === 'VE')
            ->prepend('VE')
            ->mapWithKeys(fn ($code) => [$code => country_name($code)])
            ->all();
    }

    /** @return array<string, string> */
    public function subjects(): array
    {
        return [
            'general' => __('Consulta general'),
            'quote' => __('Cotización de compras'),
            'shipping' => __('Información de envíos y tarifas'),
            'reception' => __('Recepción y almacenamiento de paquetes'),
            'consolidation' => __('Consolidación de compras'),
            'other' => __('Otro motivo'),
        ];
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255'],
            'form.whatsapp' => ['nullable', 'string', 'max:50'],
            'form.country' => ['nullable', 'string', 'size:2'],
            'form.subject' => ['required', 'string', 'max:100'],
            'form.message' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        // Create Contact Inquiry record for the admin dashboard
        $inquiry = ContactInquiry::create([
            'name' => $this->form['name'],
            'email' => $this->form['email'],
            'phone' => $this->form['whatsapp'] ?: null,
            'country' => $this->form['country'] ?: null,
            'subject' => $this->form['subject'] ?: 'general',
            'message' => $this->form['message'],
            'status' => 'unread',
        ]);

        AdminNotifier::notifyNewContactInquiry($inquiry);

        // Find or create customer record
        if ($this->form['email']) {
            $customer = Customer::where('email', $this->form['email'])->first();

            if (! $customer && $this->form['name']) {
                Customer::create([
                    'name' => $this->form['name'],
                    'email' => $this->form['email'],
                    'phone' => $this->form['whatsapp'] ?: null,
                    'country' => $this->form['country'] ?: null,
                ]);
            }
        }

        // Build WhatsApp direct URL if phone is configured
        $rawPhone = preg_replace('/\D+/', '', Setting::get('whatsapp_phone', ''));
        if ($rawPhone) {
            $subjectLabel = $this->subjects()[$this->form['subject']] ?? $this->form['subject'];
            $countryName = $this->form['country'] ? country_name($this->form['country']) : '';

            $textLines = [
                '¡Hola! Vengo desde el formulario de contacto de '.Setting::get('company_name', config('app.name')).':',
                '• *Nombre:* '.$this->form['name'],
                '• *Email:* '.$this->form['email'],
            ];

            if ($this->form['whatsapp']) {
                $textLines[] = '• *Tel/WhatsApp:* '.$this->form['whatsapp'];
            }

            if ($countryName) {
                $textLines[] = '• *País destino:* '.$countryName;
            }

            $textLines[] = '• *Asunto:* '.$subjectLabel;
            $textLines[] = '• *Mensaje:* '.$this->form['message'];

            $this->whatsappUrl = 'https://wa.me/'.$rawPhone.'?text='.urlencode(implode("\n", $textLines));
        }

        $this->sent = true;
    }

    public function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'email' => '',
            'whatsapp' => '',
            'country' => '',
            'subject' => 'general',
            'message' => '',
        ];
        $this->sent = false;
        $this->whatsappUrl = null;
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }
}
