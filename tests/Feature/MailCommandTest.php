<?php

use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

it('sends a test email via the mail:test command', function () {
    Mail::fake();

    $this->artisan('mail:test', ['email' => 'owner@example.com'])
        ->expectsOutputToContain('OK: correo enviado correctamente.')
        ->assertExitCode(0);

    Mail::assertSent(TestMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
});
