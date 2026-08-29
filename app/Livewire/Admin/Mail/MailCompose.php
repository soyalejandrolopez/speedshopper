<?php

namespace App\Livewire\Admin\Mail;

use App\Mail\AdminMessageMail;
use App\Models\Customer;
use App\Providers\MailConfigServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Testing\Fakes\MailFake;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Send Email')]
class MailCompose extends Component
{
    use WithFileUploads;

    public string $customer_id = '';

    public string $recipient = '';

    public string $subject = '';

    public string $message = '';

    /** @var array<int, mixed> */
    public array $attachments = [];

    public ?string $status = null;

    public string $statusMessage = '';

    protected function rules(): array
    {
        return [
            'recipient' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'attachments.*' => ['nullable', 'file', 'max:51200'], // Max 50MB per file (photos, videos, docs)
        ];
    }

    public function removeAttachment(int $index): void
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    public function send(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate();

        $this->applyMailConfigFromSettings();

        $attachmentFiles = [];
        foreach ($this->attachments as $uploadedFile) {
            if (is_object($uploadedFile) && method_exists($uploadedFile, 'getRealPath')) {
                $attachmentFiles[] = [
                    'path' => $uploadedFile->getRealPath(),
                    'name' => $uploadedFile->getClientOriginalName(),
                    'mime' => $uploadedFile->getMimeType(),
                ];
            }
        }

        try {
            Mail::to($this->recipient)->send(new AdminMessageMail(
                subject: $this->subject,
                body: $this->message,
                attachmentFiles: $attachmentFiles,
            ));

            $this->status = 'sent';
            $this->statusMessage = __('Email sent successfully').' ('.$this->recipient.')';
            $this->reset('subject', 'message', 'attachments');
        } catch (\Throwable $e) {
            Log::error('Admin email send failed: '.$e->getMessage());

            $this->status = 'error';
            $this->statusMessage = __('Email could not be sent').': '.$e->getMessage();
        }
    }

    public function updatedCustomerId(string $value): void
    {
        if ($value === '') {
            return;
        }

        $this->recipient = (string) (Customer::whereKey($value)->value('email') ?? '');
    }

    protected function applyMailConfigFromSettings(): void
    {
        app()->getProvider(MailConfigServiceProvider::class)?->apply();

        if (app()->bound('mailer') && app('mailer') instanceof MailFake) {
            return;
        }

        app()->forgetInstance('mailer');
    }

    public function render()
    {
        return view('livewire.admin.mail.mail-compose', [
            'customers' => Customer::query()
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }
}
