<?php

use App\Http\Requests\StoreCustomerRequest;
use App\Livewire\ClientRegistrationForm;
use Livewire\Livewire;

it('enforces character limits on customer form validation rules', function () {
    $rules = (new StoreCustomerRequest)->rules();

    expect($rules['name'])->toContain('max:255')
        ->and($rules['email'])->toContain('max:255')
        ->and($rules['phone'])->toContain('max:20')
        ->and($rules['whatsapp'])->toContain('max:20')
        ->and($rules['country'])->toContain('max:50')
        ->and($rules['city'])->toContain('max:255')
        ->and($rules['address'])->toContain('max:500');
});

it('validates character limits in ClientRegistrationForm', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', str_repeat('a', 256))
        ->set('form.whatsapp', '+123456789012345678901') // 21 chars
        ->set('form.email', str_repeat('b', 244).'@example.com') // 256 chars
        ->set('form.address', str_repeat('c', 501))
        ->call('next')
        ->assertHasErrors([
            'form.name' => 'max',
            'form.whatsapp' => 'max',
            'form.email' => 'max',
            'form.address' => 'max',
        ]);
});

it('accepts valid boundary lengths in ClientRegistrationForm', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', str_repeat('a', 255))
        ->set('form.whatsapp', '+1234567890123456789') // 20 chars
        ->set('form.email', 'valid.'.str_repeat('b', 50).'@example.com')
        ->set('form.country', 'Dominican Republic')
        ->set('form.city', str_repeat('c', 255))
        ->set('form.address', str_repeat('d', 500))
        ->set('form.services', ['online_shopping'])
        ->call('next')
        ->assertHasNoErrors(['form.name', 'form.whatsapp', 'form.email', 'form.country', 'form.city', 'form.address']);
});

it('validates step 2 field limits in ClientRegistrationForm', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('step', 2)
        ->set('form.products', str_repeat('p', 2001))
        ->set('form.preferred_stores', str_repeat('s', 256))
        ->set('form.budget', 100000000)
        ->set('form.boxes_small', 10000)
        ->call('next')
        ->assertHasErrors([
            'form.products' => 'max',
            'form.preferred_stores' => 'max',
            'form.budget' => 'max',
            'form.boxes_small' => 'max',
        ]);
});

it('accepts valid step 2 boundary lengths in ClientRegistrationForm', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('step', 2)
        ->set('form.products', str_repeat('p', 2000))
        ->set('form.preferred_stores', str_repeat('s', 255))
        ->set('form.budget', 99999999.99)
        ->set('form.boxes_small', 9999)
        ->set('form.boxes_medium', 9999)
        ->set('form.boxes_large', 9999)
        ->set('form.has_links', 'no')
        ->set('form.find_deals', 'no')
        ->call('next')
        ->assertHasNoErrors([
            'form.products',
            'form.preferred_stores',
            'form.budget',
            'form.boxes_small',
            'form.boxes_medium',
            'form.boxes_large',
            'form.has_links',
            'form.find_deals',
        ]);
});
