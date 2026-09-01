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
        try {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->validator->errors()->all())->first() ?? __('Por favor revisa los campos del formulario.');
            $this->dispatch('swal.fire', [
                'icon' => 'error',
                'title' => __('Error en el registro'),
                'text' => $firstError,
            ]);
            throw $e;
        }

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        $user->assignRole('client');

        Auth::login($user);

        session()->flash('swal_auth', 'register');
        session()->flash('success', __('¡Cuenta creada con éxito! Bienvenido a SpeedShopper.'));

        $this->redirect(route('portal.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Create Account') }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ __('Start shopping with us and track your orders from your portal.') }}</p>

    <form wire:submit="register" class="mt-6 space-y-4">
        <div>
            <label for="name" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-user text-emerald-600 text-xs"></i>
                <span>{{ __('Name') }}</span>
            </label>
            <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name"
                   placeholder="María González"
                   class="input">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label for="email" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                <span>{{ __('Email') }}</span>
            </label>
            <input wire:model="email" id="email" type="email" name="email" required autocomplete="username"
                   placeholder="maria@example.com"
                   class="input">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-lock text-emerald-600 text-xs"></i>
                <span>{{ __('Password') }}</span>
            </label>
            <div class="relative" x-data="{ show: false }">
                <input wire:model="password" id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                       placeholder="••••••••"
                       class="input pe-10">
                <button type="button" @click="show = ! show" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600"
                        :title="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'">
                    <i x-show="! show" class="fa-solid fa-eye text-sm"></i>
                    <i x-show="show" x-cloak class="fa-solid fa-eye-slash text-sm"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="password_confirmation" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-shield text-emerald-600 text-xs"></i>
                <span>{{ __('Confirm Password') }}</span>
            </label>
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
