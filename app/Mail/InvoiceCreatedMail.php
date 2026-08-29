<?php

namespace App\Mail;

use App\Enums\RequestStatus;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $companyName;

    /** @var string */
    public $locale = 'es';

    public ?string $pdfOutput;

    public string $pdfFilename;

    public float $totalCost = 0.0;

    public float $paidAmount = 0.0;

    public float $balance = 0.0;

    public bool $isUpdate = false;

    public function __construct(
        public PurchaseRequest $purchaseRequest,
        string $locale = 'es',
        ?string $pdfOutput = null,
        string $pdfFilename = 'SpeedShopper_Factura.pdf',
        bool $isUpdate = false
    ) {
        $this->locale = in_array($locale, ['es', 'en'], true) ? $locale : 'es';
        $this->pdfOutput = $pdfOutput;
        $this->pdfFilename = $pdfFilename;
        $this->isUpdate = $isUpdate;
        $this->companyName = Setting::get('company_name', 'Speed Shopper');

        $this->totalCost = (float) $this->purchaseRequest->total_cost;
        $this->paidAmount = (float) Payment::where('billable_type', PurchaseRequest::class)
            ->where('billable_id', $this->purchaseRequest->id)
            ->sum('amount_paid');
        $this->balance = max(0.0, $this->totalCost - $this->paidAmount);
    }

    public function envelope(): Envelope
    {
        $status = $this->purchaseRequest->status;
        $number = $this->purchaseRequest->number;

        if ($status === RequestStatus::Quoted) {
            $prefix = $this->isUpdate
                ? ($this->locale === 'es' ? '[Actualización] Cotización' : '[Updated] Quote')
                : ($this->locale === 'es' ? 'Cotización' : 'Quotation');
        } elseif ($status === RequestStatus::Purchased || ($this->totalCost > 0 && $this->balance <= 0)) {
            $prefix = $this->isUpdate
                ? ($this->locale === 'es' ? '[Actualización] Factura Pagada' : '[Updated] Paid Invoice')
                : ($this->locale === 'es' ? 'Factura Oficial Pagada' : 'Official Paid Invoice');
        } else {
            $prefix = $this->isUpdate
                ? ($this->locale === 'es' ? '[Actualización] Factura Pendiente' : '[Updated] Pending Invoice')
                : ($this->locale === 'es' ? 'Factura Pendiente de Pago' : 'Pending Invoice');
        }

        $subject = "{$prefix} #{$number} - {$this->companyName}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice-created-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (empty($this->pdfOutput)) {
            return [];
        }

        return [
            Attachment::fromData(fn () => $this->pdfOutput, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];
    }
}
