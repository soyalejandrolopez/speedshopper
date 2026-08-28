<?php

namespace App\Notifications;

use App\Models\ContactInquiry;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactInquiryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ContactInquiry $inquiry
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inquiry = $this->inquiry;
        $companyName = Setting::get('company_name', config('app.name'));
        $subjectText = str_replace('_', ' ', $inquiry->subject ?: 'Consulta general');

        return (new MailMessage)
            ->subject("💬 Nuevo Mensaje de Contacto — {$inquiry->name}")
            ->greeting("¡Hola Administrador de {$companyName}!")
            ->line('Has recibido un nuevo mensaje desde la página de contacto.')
            ->line("**Nombre:** {$inquiry->name}")
            ->line("**Correo Electrónico:** {$inquiry->email}")
            ->when($inquiry->phone, fn (MailMessage $m) => $m->line("**Teléfono / WhatsApp:** {$inquiry->phone}"))
            ->when($inquiry->country, fn (MailMessage $m) => $m->line('**País de Destino:** '.country_name($inquiry->country)))
            ->line('**Motivo:** '.ucfirst($subjectText))
            ->line('**Mensaje recibido:**')
            ->line(">{$inquiry->message}")
            ->action('Ver Mensajes en el Panel', route('admin.inquiries.index'))
            ->line('Puedes responderle al cliente directamente por correo o WhatsApp.');
    }
}
