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

#[Layout('layouts.app')]
#[Title('Send Email')]
class MailCompose extends Component
{
    public string $customer_id = '';

    public string $recipient = '';

    public string $subject = '';

    public string $message = '';

    public ?string $status = null;

    public string $statusMessage = '';

    protected function rules(): array
    {
        return [
            'recipient' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
        ];
    }

    public function send(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate();

        $this->applyMailConfigFromSettings();

        try {
            Mail::to($this->recipient)->send(new AdminMessageMail(
                subject: $this->subject,
                body: $this->message,
            ));

            $this->status = 'sent';
            $this->statusMessage = __('Email sent successfully').' ('.$this->recipient.')';
            $this->reset('subject', 'message');
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
