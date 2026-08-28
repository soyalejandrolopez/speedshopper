<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-4">
        <div>
            <label for="update_password_current_password" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <x-input-error :messages="$errors->get('current_password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password" class="mb-1 block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
            <input wire:model="password" id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                {{ __('Save') }}
            </button>

            <span x-data="{ shown: false, timeout: null }"
                  x-init="@this.on('password-updated', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false; }, 2000); })"
                  x-show.transition.out.opacity.duration.1500ms="shown"
                  x-cloak
                  class="text-sm font-medium text-emerald-600">
                {{ __('Saved.') }}
            </span>
        </div>
    </form>
</section>
