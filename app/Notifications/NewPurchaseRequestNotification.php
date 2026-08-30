<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPurchaseRequestNotification extends Notification
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
            ->subject("🔔 Nueva Solicitud de Compra #{$request->number} — {$customer?->name}")
            ->greeting("¡Hola Administrador de {$companyName}!")
            ->line('Se ha recibido una nueva solicitud de compra en el sistema.')
            ->line("**Número de Solicitud:** {$request->number}")
            ->line("**Cliente:** {$customer?->name}")
            ->line("**Email:** {$customer?->email}")
            ->when($customer?->whatsapp, fn (MailMessage $m) => $m->line("**Tel / WhatsApp:** {$customer->whatsapp}"))
            ->when($customer?->country, fn (MailMessage $m) => $m->line('**País de Destino:** '.country_name($customer->country)))
            ->line("**Producto solicitado:** {$request->product_name}")
            ->when($request->store, fn (MailMessage $m) => $m->line("**Tienda preferida:** {$request->store}"))
            ->when($request->quantity > 1, fn (MailMessage $m) => $m->line("**Cantidad:** {$request->quantity}"))
            ->when($request->total_cost > 0, fn (MailMessage $m) => $m->line('**Presupuesto / Total Estimado:** '.money($request->total_cost)))
            ->when($request->product_url, fn (MailMessage $m) => $m->line("**Enlace del producto:** {$request->product_url}"));

        if (! empty($request->services)) {
            $options = service_options();
            $serviceLabels = array_map(fn ($s) => $options[$s] ?? $s, $request->services);
            $mail->line('**Servicios requeridos:** '.implode(', ', $serviceLabels));
        }

        if ($this->pdfContent) {
            $mail->attachData($this->pdfContent, "Cotizacion-{$request->number}.pdf", [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail
            ->action('Ver Solicitud en el Panel', route('admin.requests.show', $request))
            ->line('Se adjunta el documento PDF de la cotización estimada para su revisión.');
    }
}
