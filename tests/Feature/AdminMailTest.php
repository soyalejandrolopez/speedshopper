<?php

use App\Livewire\Admin\Mail\MailCompose;
use App\Mail\AdminMessageMail;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('renders the send email page for admins', function () {
    $this->actingAs(createAdmin());

    Livewire::test(MailCompose::class)
        ->assertOk()
        ->assertSee(__('Send Email'));
});

it('sends an email', function () {
    $this->actingAs(createAdmin());
    Mail::fake();

    Livewire::test(MailCompose::class)
        ->set('recipient', 'cliente@example.com')
        ->set('subject', 'Saludo desde la tienda')
        ->set('message', 'Hola, este es un mensaje.')
        ->call('send')
        ->assertHasNoErrors()
        ->assertSet('status', 'sent')
        ->assertSet('subject', '')
        ->assertSet('message', '');

    Mail::assertSent(AdminMessageMail::class, function ($mail) {
        return $mail->hasTo('cliente@example.com')
            && $mail->subject === 'Saludo desde la tienda'
            && $mail->body === 'Hola, este es un mensaje.';
    });
});

it('validates the compose fields', function () {
    $this->actingAs(createAdmin());
    Mail::fake();

    Livewire::test(MailCompose::class)
        ->call('send')
        ->assertHasErrors(['recipient', 'subject', 'message']);

    Mail::assertNothingSent();
});

it('rejects an invalid recipient email', function () {
    $this->actingAs(createAdmin());
    Mail::fake();

    Livewire::test(MailCompose::class)
        ->set('recipient', 'not-an-email')
        ->set('subject', 'Asunto')
        ->set('message', 'Mensaje')
        ->call('send')
        ->assertHasErrors('recipient');

    Mail::assertNothingSent();
});

it('prefills the recipient when a customer is selected', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::create([
        'name' => 'María Pérez',
        'email' => 'maria@example.com',
    ]);

    Livewire::test(MailCompose::class)
        ->set('customer_id', (string) $customer->id)
        ->assertSet('recipient', 'maria@example.com');
});
