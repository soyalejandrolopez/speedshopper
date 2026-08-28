<?php

use App\Livewire\Admin\Inquiries\InquiriesIndex;
use App\Livewire\ClientRegistrationForm;
use App\Livewire\ContactForm;
use App\Models\ContactInquiry;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;

it('renders the contact page with header, footer and the contact form', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee(__('Contáctanos'))
        ->assertSeeLivewire(ContactForm::class);

    $this->get('/contact')
        ->assertOk();
});

it('lists the 11 required destination countries in ClientRegistrationForm', function () {
    $component = Livewire::test(ClientRegistrationForm::class);
    $countries = $component->instance()->countries();

    expect(array_keys($countries))->toBe([
        'VE', 'CO', 'EC', 'PE', 'CL', 'CR', 'PA', 'DO', 'SV', 'HN', 'MX',
    ]);

    expect($countries['VE'])->toBe('Venezuela')
        ->and($countries['CO'])->toBe('Colombia')
        ->and($countries['EC'])->toBe('Ecuador')
        ->and($countries['PE'])->toBe('Perú')
        ->and($countries['CL'])->toBe('Chile')
        ->and($countries['CR'])->toBe('Costa Rica')
        ->and($countries['PA'])->toBe('Panamá')
        ->and($countries['DO'])->toBe('República Dominicana')
        ->and($countries['SV'])->toBe('El Salvador')
        ->and($countries['HN'])->toBe('Honduras')
        ->and($countries['MX'])->toBe('México');
});

it('lists the 11 destination countries in ContactForm', function () {
    $component = Livewire::test(ContactForm::class);
    $countries = $component->instance()->countries();

    expect(array_keys($countries))->toBe([
        'VE', 'CO', 'EC', 'PE', 'CL', 'CR', 'PA', 'DO', 'SV', 'HN', 'MX',
    ]);
});

it('displays the fee tiers in the client registration form', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('step', 2)
        ->assertSee('Compras de $100 a $699')
        ->assertSee('Fee: 20%')
        ->assertSee('Incluye hasta 2 tiendas y 2 horas de servicio.')
        ->assertSee('Compras de $700 a $1,499')
        ->assertSee('Fee: 15%')
        ->assertSee('Incluye hasta 3 tiendas y 3 horas de servicio.')
        ->assertSee('Compras de $1,500 o más')
        ->assertSee('Fee: 15%')
        ->assertSee('Incluye hasta 4 tiendas y 4 horas de servicio.');
});

it('submits contact form and creates customer, contact inquiry and WhatsApp URL', function () {
    Setting::set('whatsapp_phone', '+12815551234');

    Livewire::test(ContactForm::class)
        ->set('form.name', 'Carlos Mendoza')
        ->set('form.email', 'carlos@example.com')
        ->set('form.whatsapp', '+584121234567')
        ->set('form.country', 'VE')
        ->set('form.subject', 'quote')
        ->set('form.message', 'Hola, deseo cotizar una laptop en Amazon.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    $customer = Customer::where('email', 'carlos@example.com')->first();
    expect($customer)->not->toBeNull()
        ->and($customer->name)->toBe('Carlos Mendoza')
        ->and($customer->country)->toBe('VE');

    $inquiry = ContactInquiry::where('email', 'carlos@example.com')->first();
    expect($inquiry)->not->toBeNull()
        ->and($inquiry->name)->toBe('Carlos Mendoza')
        ->and($inquiry->country)->toBe('VE')
        ->and($inquiry->status)->toBe('unread')
        ->and($inquiry->message)->toBe('Hola, deseo cotizar una laptop en Amazon.');
});

it('shows contact inquiries in the admin dashboard', function () {
    seedRoles();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    ContactInquiry::factory()->create([
        'name' => 'Ana Morales',
        'email' => 'ana@example.com',
        'status' => 'unread',
        'message' => 'Necesito información de envíos a Venezuela.',
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Ana Morales')
        ->assertSee('ana@example.com')
        ->assertSee('Mensajes de Contacto');
});

it('allows admin to manage inquiries in the admin inquiries page', function () {
    seedRoles();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $inquiry = ContactInquiry::factory()->create([
        'name' => 'Roberto Gómez',
        'email' => 'roberto@example.com',
        'status' => 'unread',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.inquiries.index'))
        ->assertOk()
        ->assertSee('Roberto Gómez')
        ->assertSee('roberto@example.com');

    Livewire::actingAs($admin)
        ->test(InquiriesIndex::class)
        ->call('markAsRead', $inquiry->id);

    expect($inquiry->fresh()->status)->toBe('read');
});
