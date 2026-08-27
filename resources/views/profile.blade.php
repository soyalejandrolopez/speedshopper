@php
    $layout = auth()->user()->isAdmin() ? 'account::app' : 'account::portal';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">{{ __('Profile') }}</x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <livewire:profile.update-profile-information-form />
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <livewire:profile.update-password-form />
        </div>
    </div>
</x-dynamic-component>
