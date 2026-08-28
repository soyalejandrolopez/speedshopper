<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: Auth::user()->dashboardRoute(), navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Welcome back') }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ __('Log in to your account to track your orders and packages.') }}</p>

    <form wire:submit="login" class="mt-6 space-y-4">
        <div>
            <label for="email" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                <span>{{ __('Email') }}</span>
            </label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                   placeholder="maria@example.com"
                   class="input">
            <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
        </div>

        <div>
            <div class="mb-1 flex items-center justify-between">
                <label for="password" class="label mb-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-emerald-600 text-xs"></i>
                    <span>{{ __('Password') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate class="text-xs font-medium text-emerald-700 hover:text-emerald-900">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <div class="relative" x-data="{ show: false }">
                <input wire:model="form.password" id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                       placeholder="••••••••"
                       class="input pe-10">
                <button type="button" @click="show = ! show" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600"
                        :title="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'">
                    <i x-show="! show" class="fa-solid fa-eye text-sm"></i>
                    <i x-show="show" x-cloak class="fa-solid fa-eye-slash text-sm"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        <label for="remember" class="flex items-center gap-2 text-sm text-gray-600">
            <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            {{ __('Remember me') }}
        </label>

        <button type="submit" class="btn-primary w-full">
            {{ __('Log in') }}
        </button>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-4 text-center text-sm text-gray-500">
        {{ __('Don\'t have an account?') }}
        <a href="{{ route('register') }}" wire:navigate class="font-semibold text-emerald-700 hover:text-emerald-900">
            {{ __('Register') }}
        </a>
    </div>
</div>
