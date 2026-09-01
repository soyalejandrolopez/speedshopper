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
        try {
            $this->validate([
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->validator->errors()->all())->first() ?? __('Por favor ingresa un correo válido.');
            $this->dispatch('swal.fire', [
                'icon' => 'warning',
                'title' => __('Revisa el correo ingresado'),
                'text' => $firstError,
            ]);
            throw $e;
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $msg = __($status);
            $this->addError('email', $msg);
            $this->dispatch('swal.fire', [
                'icon' => 'error',
                'title' => __('No se pudo enviar el enlace'),
                'text' => $msg,
            ]);

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
        $this->dispatch('swal.fire', [
            'icon' => 'success',
            'title' => __('¡Enlace enviado!'),
            'text' => __('Hemos enviado el enlace para restablecer tu contraseña a tu correo electrónico.'),
        ]);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
        <i class="fa-solid fa-envelope text-2xl"></i>
    </div>

    <h1 class="mt-4 text-xl font-bold text-gray-900">{{ __('Forgot your password?') }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}</p>

    <form wire:submit="sendPasswordResetLink" class="mt-6 space-y-4">
        <div>
            <label for="email" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                <span>{{ __('Email') }}</span>
            </label>
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
