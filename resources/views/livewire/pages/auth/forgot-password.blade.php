<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send an email password reset link to the user.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
        </svg>
    </div>

    <h1 class="mt-4 text-xl font-bold text-gray-900">{{ __('Forgot your password?') }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}</p>

    <form wire:submit="sendPasswordResetLink" class="mt-6 space-y-4">
        <div>
            <label for="email" class="label">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" name="email" required autofocus
                   placeholder="maria@example.com"
                   class="input">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full">
            {{ __('Email Password Reset Link') }}
        </button>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-4 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-emerald-700 hover:text-emerald-900">
            {{ __('Back to login') }}
        </a>
    </div>
</div>
