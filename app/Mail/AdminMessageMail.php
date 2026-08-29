<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $body;

    /** @var array<int, array{path: string, name: string, mime: ?string}> */
    public array $attachmentFiles;

    /**
     * @param  array<int, array{path: string, name: string, mime: ?string}>  $attachmentFiles
     */
    public function __construct(
        string $subject,
        string $body,
        array $attachmentFiles = [],
    ) {
        $this->subject = $subject;
        $this->body = $body;
        $this->attachmentFiles = $attachmentFiles;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-message',
            with: [
                'attachmentFiles' => $this->attachmentFiles,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $mailAttachments = [];

        foreach ($this->attachmentFiles as $file) {
            if (! empty($file['path']) && file_exists($file['path'])) {
                $attachment = Attachment::fromPath($file['path']);

                if (! empty($file['name'])) {
                    $attachment->as($file['name']);
                }

                if (! empty($file['mime'])) {
                    $attachment->withMime($file['mime']);
                }

                $mailAttachments[] = $attachment;
            }
        }

        return $mailAttachments;
    }
}
