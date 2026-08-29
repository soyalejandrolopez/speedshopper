<?php

namespace App\Mail;

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

    public function __construct(
        public PurchaseRequest $purchaseRequest,
        string $locale = 'es',
        ?string $pdfOutput = null,
        string $pdfFilename = 'SpeedShopper_Factura.pdf'
    ) {
        $this->locale = in_array($locale, ['es', 'en'], true) ? $locale : 'es';
        $this->pdfOutput = $pdfOutput;
        $this->pdfFilename = $pdfFilename;
        $this->companyName = Setting::get('company_name', 'Speed Shopper');

        $this->totalCost = (float) $this->purchaseRequest->total_cost;
        $this->paidAmount = (float) Payment::where('billable_type', PurchaseRequest::class)
            ->where('billable_id', $this->purchaseRequest->id)
            ->sum('amount_paid');
        $this->balance = max(0.0, $this->totalCost - $this->paidAmount);
    }

    public function envelope(): Envelope
    {
        $subject = $this->locale === 'es'
            ? "Factura Oficial #{$this->purchaseRequest->number} - {$this->companyName}"
            : "Official Invoice #{$this->purchaseRequest->number} - {$this->companyName}";

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
