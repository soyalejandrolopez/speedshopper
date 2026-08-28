<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: Auth::user()->dashboardRoute(), navigate: true);
    }
}; ?>

<div>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
        <i class="fa-solid fa-lock text-2xl"></i>
    </div>

    <h1 class="mt-4 text-xl font-bold text-gray-900">{{ __('Confirm Password') }}</h1>

    <p class="mt-2 text-sm leading-relaxed text-gray-500">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form wire:submit="confirmPassword" class="mt-6 space-y-4">
        <div>
            <label for="password" class="label">{{ __('Password') }}</label>
            <input wire:model="password" id="password" type="password" name="password" required autofocus autocomplete="current-password"
                   placeholder="••••••••"
                   class="input">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full">
            {{ __('Confirm') }}
        </button>
    </form>
</div>
