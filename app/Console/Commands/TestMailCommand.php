<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email? : Correo destino}
        {--host= : Host SMTP (opcional)}
        {--port= : Puerto SMTP (opcional)}
        {--user= : Usuario SMTP (opcional)}
        {--pass= : Contraseña SMTP (opcional)}
        {--encryption= : ssl | tls | "" (opcional)}';

    protected $description = 'Envía un correo de prueba y muestra el resultado';

    public function handle(): int
    {
        $email = $this->argument('email') ?: 'test@example.com';

        if ($this->option('host') || $this->option('user')) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $this->option('host') ?: config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => (int) ($this->option('port') ?: config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.username' => $this->option('user') ?: config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $this->option('pass') ?: config('mail.mailers.smtp.password'),
                'mail.mailers.smtp.encryption' => $this->option('encryption') ?: config('mail.mailers.smtp.encryption'),
                'mail.mailers.smtp.timeout' => 15,
            ]);
        }

        $this->info('Enviando correo de prueba a: '.$email);
        $this->line('Mailer: '.config('mail.default'));
        $this->line('Host: '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port'));

        try {
            Mail::to($email)->send(new TestMail());

            $this->info('OK: correo enviado correctamente.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('FALLO: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
