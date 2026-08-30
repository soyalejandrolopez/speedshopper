<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientPurchaseRequestConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PurchaseRequest $purchaseRequest,
        public ?string $pdfContent = null
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
        $request = $this->purchaseRequest;
        $customer = $request->customer;
        $companyName = Setting::get('company_name', config('app.name'));

        $mail = (new MailMessage)
            ->subject("📄 Recibimos tu Solicitud de Compra #{$request->number} — {$companyName}")
            ->greeting("¡Hola {$customer?->name}!")
            ->line("Hemos recibido tu solicitud de compra exitosamente en **{$companyName}**.")
            ->line("**Número de Solicitud:** #{$request->number}")
            ->line("**Producto:** {$request->product_name}")
            ->when($request->quantity > 1, fn (MailMessage $m) => $m->line("**Cantidad:** {$request->quantity}"))
            ->when($request->total_cost > 0, fn (MailMessage $m) => $m->line('**Total Estimado:** '.money($request->total_cost)));

        if (! empty($request->services)) {
            $options = service_options();
            $serviceLabels = array_map(fn ($s) => $options[$s] ?? $s, $request->services);
            $mail->line('**Servicios seleccionados:** '.implode(', ', $serviceLabels));
        }

        $mail->line('Adjunto a este correo encontrarás el **archivo PDF de tu cotización estimada** con el desglose completo de productos, comisiones y costos de embalaje.');

        if ($this->pdfContent) {
            $mail->attachData($this->pdfContent, "Cotizacion-{$request->number}.pdf", [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail
            ->action('Ver mi Solicitud en el Portal', route('portal.requests.show', $request))
            ->line('Nuestro equipo revisará tu solicitud y te contactará a la brevedad para coordinar tu compra.');
    }
}
