<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PricingRatesMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $companyName;

    /** @var string */
    public $locale = 'es';

    public ?string $customMessage;

    public string $pdfOutput;

    public string $pdfFilename;

    public function __construct(
        string $locale = 'es',
        ?string $customMessage = null,
        string $pdfOutput = '',
        string $pdfFilename = 'SpeedShopper_Tarifas.pdf'
    ) {
        $this->locale = in_array($locale, ['es', 'en'], true) ? $locale : 'es';
        $this->customMessage = $customMessage;
        $this->pdfOutput = $pdfOutput;
        $this->pdfFilename = $pdfFilename;
        $this->companyName = Setting::get('company_name', 'Speed Shopper');
    }

    public function envelope(): Envelope
    {
        $subject = $this->locale === 'es'
            ? "Tarifas y Lista de Precios Oficiales - {$this->companyName}"
            : "Official Price List & Services Guide - {$this->companyName}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.pricing-rates-mail',
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
