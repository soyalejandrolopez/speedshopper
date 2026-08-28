<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        session()->flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
        <i class="fa-solid fa-lock text-2xl"></i>
    </div>

    <h1 class="mt-4 text-xl font-bold text-gray-900">{{ __('Reset your password') }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ __('Choose a new password for your account.') }}</p>

    <form wire:submit="resetPassword" class="mt-6 space-y-4">
        <div>
            <label for="email" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                <span>{{ __('Email') }}</span>
            </label>
            <input wire:model="email" id="email" type="email" name="email" required autocomplete="username"
                   class="input">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="label flex items-center gap-1.5">
                <i class="fa-solid fa-lock text-emerald-600 text-xs"></i>
                <span>{{ __('New Password') }}</span>
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
            {{ __('Reset Password') }}
        </button>
    </form>
</div>
