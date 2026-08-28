<div>
    <x-slot name="header">{{ __('Send Email') }}</x-slot>

    @if ($status === 'sent')
        <div class="mb-4 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
            <i class="fa-solid fa-circle-check text-base"></i>
            {{ $statusMessage }}
        </div>
    @elseif ($status === 'error')
        <div class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-inset ring-red-200">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-base shrink-0"></i>
            <span class="break-all">{{ $statusMessage }}</span>
        </div>
    @endif

    <form wire:submit="send" class="max-w-3xl space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-paper-plane text-base"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('New Message') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Send an email using the configured SMTP account.') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 p-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Customer') }}</label>
                    <select wire:model.live="customer_id" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">— {{ __('Type a recipient below') }} —</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} · {{ $customer->email }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Selecting a customer fills the recipient automatically.') }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="recipient-{{ $this->getId() }}">{{ __('To') }} *</label>
                    <input id="recipient-{{ $this->getId() }}" name="recipient" type="email" wire:model="recipient"
                           placeholder="cliente@example.com"
                           class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('recipient') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="subject-{{ $this->getId() }}">{{ __('Subject') }} *</label>
                    <input id="subject-{{ $this->getId() }}" name="subject" type="text" wire:model="subject"
                           placeholder="{{ __('Message subject') }}"
                           class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="message-{{ $this->getId() }}">{{ __('Message') }} *</label>
                    <textarea id="message-{{ $this->getId() }}" name="message" wire:model="message" rows="8"
                              placeholder="{{ __('Write the email body here...') }}"
                              class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-gray-400">
                <i class="fa-solid fa-circle-info text-sm me-1"></i>
                {{ __('The email is sent from the address configured in Mail / SMTP settings.') }}
            </p>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_8px_20px_rgba(16,185,129,0.3)] transition-all duration-300 hover:from-emerald-400 hover:to-emerald-500 hover:shadow-[0_12px_25px_rgba(16,185,129,0.4)] active:scale-[0.97]">
                <i class="fa-solid fa-paper-plane text-base"></i>
                {{ __('Send Email') }}
            </button>
        </div>
    </form>
</div>
