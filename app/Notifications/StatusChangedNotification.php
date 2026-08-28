<?php

namespace App\Notifications;

use App\Enums\PackageStatus;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
use App\Models\Package;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Shipment;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedNotification extends Notification
{
    public function __construct(
        public mixed $statusable,
        public string $from,
        public string $to,
    ) {}

    /** @return list<class-string> */
    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->newStatusLabel();

        return (new MailMessage)
            ->subject($this->subject())
            ->greeting(__('Hello').'!')
            ->line($this->summaryLine())
            ->line(__('Current status').': **'.$label.'**')
            ->when($note = $this->transitionNote(), fn (MailMessage $m) => $m->line(__('Note').': '.$note))
            ->action(__('View in your portal'), route('portal.dashboard'))
            ->line(__('Thanks for trusting us with your orders.'));
    }

    public function toWhatsApp(object $notifiable): ?string
    {
        $label = $this->newStatusLabel();

        $message = '📦 *'.Setting::get('company_name', config('app.name'))."*\n\n";
        $message .= $this->summaryLine()."\n";
        $message .= __('Current status').': '.$label."\n";

        if ($note = $this->transitionNote()) {
            $message .= __('Note').': '.$note."\n";
        }

        $message .= "\n".route('portal.dashboard');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'number' => $this->statusable->number ?? null,
            'from' => $this->from,
            'to' => $this->to,
        ];
    }

    protected function subject(): string
    {
        $label = $this->newStatusLabel();

        return __('Status updated').' — '.($this->statusable->number ?? '').': '.$label;
    }

    protected function summaryLine(): string
    {
        $statusable = $this->statusable;

        return match (true) {
            $statusable instanceof PurchaseRequest => __('Your request').' '.$statusable->number.' ('.$statusable->product_name.')',
            $statusable instanceof Package => __('Your package').' '.$statusable->number.($statusable->store ? ' ('.$statusable->store.')' : ''),
            $statusable instanceof Shipment => __('Your box').' '.$statusable->number.($statusable->carrier ? ' ('.$statusable->carrier.')' : ''),
            default => $statusable->number ?? __('Your order'),
        };
    }

    protected function newStatusLabel(): string
    {
        return match (true) {
            $this->statusable instanceof PurchaseRequest => RequestStatus::from($this->to)->label(),
            $this->statusable instanceof Package => PackageStatus::from($this->to)->label(),
            $this->statusable instanceof Shipment => ShipmentStatus::from($this->to)->label(),
            default => $this->to,
        };
    }

    protected function transitionNote(): ?string
    {
        $history = $this->statusable->statusHistory?->first();

        return $history?->note;
    }
}
