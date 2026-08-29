<?php

use App\Livewire\Admin\Mail\MailCompose;
use App\Mail\AdminMessageMail;
use App\Models\Customer;
use Illuminate\Http\UploadedFile;
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

it('sends an email with photo and video attachments', function () {
    $this->actingAs(createAdmin());
    Mail::fake();

    $photo = UploadedFile::fake()->image('paquete_recibido.jpg');
    $video = UploadedFile::fake()->create('video_revision.mp4', 2048, 'video/mp4');

    Livewire::test(MailCompose::class)
        ->set('recipient', 'cliente.fotos@example.com')
        ->set('subject', 'Fotos y video de tus compras')
        ->set('message', 'Adjuntamos las fotos y el video de la inspección.')
        ->set('attachments', [$photo, $video])
        ->call('send')
        ->assertHasNoErrors()
        ->assertSet('status', 'sent')
        ->assertSet('attachments', []);

    Mail::assertSent(AdminMessageMail::class, function ($mail) {
        return $mail->hasTo('cliente.fotos@example.com')
            && $mail->subject === 'Fotos y video de tus compras'
            && count($mail->attachmentFiles) === 2
            && $mail->attachmentFiles[0]['name'] === 'paquete_recibido.jpg'
            && $mail->attachmentFiles[1]['name'] === 'video_revision.mp4';
    });
});

it('allows removing an attachment before sending', function () {
    $this->actingAs(createAdmin());

    $photo = UploadedFile::fake()->image('foto1.jpg');
    $photo2 = UploadedFile::fake()->image('foto2.jpg');

    Livewire::test(MailCompose::class)
        ->set('attachments', [$photo, $photo2])
        ->call('removeAttachment', 0)
        ->assertCount('attachments', 1);
});
