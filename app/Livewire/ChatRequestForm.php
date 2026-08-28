<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Services\AdminNotifier;
use Livewire\Component;

class ChatRequestForm extends Component
{
    public int $step = 1;

    public string $currentAnswer = '';

    public string $name = '';

    public string $email = '';

    public string $whatsapp = '';

    public string $product_name = '';

    public string $product_url = '';

    public string $description = '';

    public bool $finished = false;

    public bool $started = false;

    public const TOTAL_STEPS = 6;

    public function start(): void
    {
        $this->started = true;
    }

    public function back(): void
    {
        if ($this->finished || $this->step <= 1) {
            return;
        }

        $this->step--;
        $this->currentAnswer = $this->answers()[$this->step];
    }

    public function progressPercent(): int
    {
        if ($this->finished) {
            return 100;
        }

        return (int) round((($this->step - 1) / self::TOTAL_STEPS) * 100);
    }

    public function next(): void
    {
        $this->validateCurrent();
        $this->storeAndAdvance();
    }

    public function skip(): void
    {
        $this->currentAnswer = '';
        $this->storeAndAdvance();
    }

    public function resetChat(): void
    {
        $this->reset();
    }

    public function finish(): void
    {
        $customer = null;

        if ($this->email || $this->whatsapp) {
            $customer = Customer::query()
                ->where(function ($q) {
                    if ($this->email) {
                        $q->where('email', $this->email);
                    }
                    if ($this->whatsapp) {
                        $q->orWhere('whatsapp', $this->whatsapp);
                    }
                })
                ->first();
        }

        $customer ??= Customer::create([
            'name' => $this->name,
            'email' => $this->email ?: null,
            'whatsapp' => $this->whatsapp ?: null,
            'registered_at' => today(),
        ]);

        $request = $customer->purchaseRequests()->create([
            'product_name' => $this->product_name ?: __('Chat consultation'),
            'product_url' => $this->product_url ?: null,
            'description' => $this->description ?: null,
            'status' => 'new',
        ]);

        AdminNotifier::notifyNewPurchaseRequest($request);

        $this->finished = true;
    }

    /** @return array<int, string> */
    public function questions(): array
    {
        return [
            1 => __('Hi! What is your name?'),
            2 => __('Great, thanks! What is your email?'),
            3 => __('And your WhatsApp or phone? (optional)'),
            4 => __('What product would you like to buy?'),
            5 => __('Do you have the product link? (optional)'),
            6 => __('Any size, color or extra details? (optional)'),
        ];
    }

    /** @return array<int, string> */
    public function answers(): array
    {
        return [
            1 => $this->name,
            2 => $this->email,
            3 => $this->whatsapp,
            4 => $this->product_name,
            5 => $this->product_url,
            6 => $this->description,
        ];
    }

    public function placeholder(int $step): string
    {
        return match ($step) {
            1 => 'María González',
            2 => 'you@example.com',
            3 => '+502 5555 0000',
            4 => 'Nike Air Max 270 — Talla 7.5',
            5 => 'https://...',
            default => __('Size, color, brand, anything we should know...'),
        };
    }

    public function inputType(int $step): string
    {
        return $step === 2 ? 'email' : 'text';
    }

    public function isInputLong(int $step): bool
    {
        return $step === 6;
    }

    public function isRequiredStep(int $step): bool
    {
        return in_array($step, [1, 2, 4], true);
    }

    protected function validateCurrent(): void
    {
        $this->validate([
            'currentAnswer' => match ($this->step) {
                1 => ['required', 'string', 'max:255'],
                2 => ['required', 'email', 'max:255'],
                3 => ['nullable', 'string', 'max:50'],
                4 => ['required', 'string', 'max:255'],
                5 => ['nullable', 'url:http,https', 'max:2048'],
                6 => ['nullable', 'string', 'max:2000'],
                default => ['nullable'],
            },
        ]);
    }

    protected function storeAndAdvance(): void
    {
        match ($this->step) {
            1 => $this->name = $this->currentAnswer,
            2 => $this->email = $this->currentAnswer,
            3 => $this->whatsapp = $this->currentAnswer,
            4 => $this->product_name = $this->currentAnswer,
            5 => $this->product_url = $this->currentAnswer,
            6 => $this->description = $this->currentAnswer,
        };

        $this->currentAnswer = '';
        $this->step++;

        if ($this->step > self::TOTAL_STEPS) {
            $this->finish();
        }
    }

    public function render()
    {
        return view('livewire.chat-request-form');
    }
}
