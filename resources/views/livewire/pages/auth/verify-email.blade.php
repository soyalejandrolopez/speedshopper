<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: Auth::user()->dashboardRoute(), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(): void
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="text-center">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
        <i class="fa-solid fa-envelope text-base h-7 w-7"></i>
    </div>

    <h1 class="mt-4 text-xl font-bold text-gray-900">{{ __('Thanks for signing up!') }}</h1>

    <p class="mt-2 text-sm leading-relaxed text-gray-500">
        {{ __('Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <p class="mt-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </p>
    @endif

    <div class="mt-6 flex flex-col gap-2">
        <button wire:click="sendVerification" type="button" class="btn-primary w-full">
            {{ __('Resend Verification Email') }}
        </button>

        <button wire:click="logout" type="button" class="btn-ghost w-full">
            {{ __('Log Out') }}
        </button>
    </div>
</div>
