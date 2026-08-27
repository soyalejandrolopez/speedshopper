<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        $user->assignRole('client');

        Auth::login($user);

        $this->redirect(route('portal.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Create Account') }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ __('Start shopping with us and track your orders from your portal.') }}</p>

    <form wire:submit="register" class="mt-6 space-y-4">
        <div>
            <label for="name" class="label">{{ __('Name') }}</label>
            <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name"
                   placeholder="María González"
                   class="input">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label for="email" class="label">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" name="email" required autocomplete="username"
                   placeholder="maria@example.com"
                   class="input">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="label">{{ __('Password') }}</label>
            <div class="relative" x-data="{ show: false }">
                <input wire:model="password" id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                       placeholder="••••••••"
                       class="input pe-10">
                <button type="button" @click="show = ! show" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600"
                        :title="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'">
                    <svg x-show="! show" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg x-show="show" x-cloak class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="password_confirmation" class="label">{{ __('Confirm Password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="••••••••"
                   class="input">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full">
            {{ __('Register') }}
        </button>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-4 text-center text-sm text-gray-500">
        {{ __('Already registered?') }}
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-emerald-700 hover:text-emerald-900">
            {{ __('Log in') }}
        </a>
    </div>
</div>
