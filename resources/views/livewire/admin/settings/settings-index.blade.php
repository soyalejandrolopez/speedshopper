<div>
    <x-slot name="header">{{ __('Fees & Settings') }}</x-slot>

    <form wire:submit="save" class="max-w-3xl space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('General') }}</h3>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="company_name-{{ $this->getId() }}">{{ __('Company Name') }}</label>
                    <input id="company_name-{{ $this->getId() }}" name="company_name" type="text" wire:model="settings.company_name" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="currency-{{ $this->getId() }}">{{ __('Currency') }}</label>
                    <input id="currency-{{ $this->getId() }}" name="currency" type="text" wire:model="settings.currency" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="warehouse_address-{{ $this->getId() }}">{{ __('Warehouse Address') }}</label>
                    <input id="warehouse_address-{{ $this->getId() }}" name="warehouse_address" type="text" wire:model="settings.warehouse_address" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="whatsapp_phone-{{ $this->getId() }}">{{ __('WhatsApp Phone') }}</label>
                    <input id="whatsapp_phone-{{ $this->getId() }}" name="whatsapp_phone" type="text" wire:model="settings.whatsapp_phone" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="countries_served-{{ $this->getId() }}">{{ __('Countries Served (ISO2)') }}</label>
                    <input id="countries_served-{{ $this->getId() }}" name="countries_served" type="text" wire:model="settings.countries_served" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <p class="mt-1 text-xs text-gray-400">{{ __('e.g.') }} MX,GT,HN,SV,NI,CR,PA,CO,EC,PE,CL,AR</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Fees Configuration') }}</h3>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="shopper_fee-{{ $this->getId() }}">{{ __('Shopper Fee') }}</label>
                    <input id="shopper_fee-{{ $this->getId() }}" name="shopper_fee" type="number" step="0.01" min="0" wire:model="settings.shopper_fee" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700" for="shopper_fee_is_percent-{{ $this->getId() }}">
                        <input id="shopper_fee_is_percent-{{ $this->getId() }}" name="shopper_fee_is_percent" type="checkbox" wire:model="settings.shopper_fee_is_percent" value="1"
                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        {{ __('Shopper Fee is Percent') }}
                    </label>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="receiving_fee-{{ $this->getId() }}">{{ __('Receiving Fee') }}</label>
                    <input id="receiving_fee-{{ $this->getId() }}" name="receiving_fee" type="number" step="0.01" min="0" wire:model="settings.receiving_fee" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="packing_fee-{{ $this->getId() }}">{{ __('Packing Fee') }}</label>
                    <input id="packing_fee-{{ $this->getId() }}" name="packing_fee" type="number" step="0.01" min="0" wire:model="settings.packing_fee" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Notifications') }}</h3>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2">
                <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-3 text-sm text-gray-700" for="notify_email-{{ $this->getId() }}">
                    <input id="notify_email-{{ $this->getId() }}" name="notify_email" type="checkbox" wire:model="settings.notify_email" value="1"
                           class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="font-medium text-gray-900">{{ __('Email notifications') }}</span>
                        <span class="block text-xs text-gray-500">{{ __('Notify the customer by email when an order, package or shipment changes status.') }}</span>
                    </span>
                </label>
                <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-3 text-sm text-gray-700" for="notify_whatsapp-{{ $this->getId() }}">
                    <input id="notify_whatsapp-{{ $this->getId() }}" name="notify_whatsapp" type="checkbox" wire:model="settings.notify_whatsapp" value="1"
                           class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="font-medium text-gray-900">{{ __('WhatsApp notifications') }}</span>
                        <span class="block text-xs text-gray-500">{{ __('Send an automatic WhatsApp message on status changes.') }}</span>
                    </span>
                </label>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="whatsapp_api_url-{{ $this->getId() }}">{{ __('WhatsApp API URL') }}</label>
                    <input id="whatsapp_api_url-{{ $this->getId() }}" name="whatsapp_api_url" type="text" wire:model="settings.whatsapp_api_url"
                           placeholder="https://..." class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <p class="mt-1 text-xs text-gray-400">{{ __('Receives a POST with phone and message. Leave empty to skip WhatsApp.') }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="whatsapp_api_token-{{ $this->getId() }}">{{ __('WhatsApp API Token') }}</label>
                    <input id="whatsapp_api_token-{{ $this->getId() }}" name="whatsapp_api_token" type="password" wire:model="settings.whatsapp_api_token"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Branding') }}</h3>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Upload your company logo and favicon. They are used across the website, portal and reports.') }}</p>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Logo') }}</label>
                    <div class="flex items-start gap-4">
                        <span class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                            @if (brand_logo_url())
                                <img src="{{ brand_logo_url() }}" alt="{{ __('Logo') }}" class="h-full w-full object-contain p-1">
                            @else
                                <x-brand-logo size="lg" />
                            @endif
                        </span>
                        <div class="flex-1 space-y-2">
                            <input type="file" wire:model="logo" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:me-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                            @error('logo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            @if (brand_logo_url())
                                <button type="button" wire:click="removeLogo" wire:confirm="{{ __('Are you sure you want to delete this record?') }}"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700">
                                    {{ __('Remove Logo') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Favicon') }}</label>
                    <div class="flex items-start gap-4">
                        <span class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                            @if (brand_favicon_url())
                                <img src="{{ brand_favicon_url() }}" alt="{{ __('Favicon') }}" class="h-8 w-8 object-contain">
                            @else
                                <svg class="h-7 w-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                                </svg>
                            @endif
                        </span>
                        <div class="flex-1 space-y-2">
                            <input type="file" wire:model="favicon" accept="image/x-icon,.png,.svg,.webp,.ico"
                                   class="block w-full text-sm text-gray-500 file:me-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                            @error('favicon') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            @if (brand_favicon_url())
                                <button type="button" wire:click="removeFavicon" wire:confirm="{{ __('Are you sure you want to delete this record?') }}"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700">
                                    {{ __('Remove Favicon') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Site Theme') }}</h3>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Pick the main color of the website, portal and reports. It applies everywhere automatically.') }}</p>
            </div>
            <div class="space-y-4 p-5">
                <div class="flex flex-wrap items-center gap-3">
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">
                        <span class="h-8 w-8 rounded-lg border border-gray-300 shadow-inner" style="background-color: {{ $settings['theme_color'] }}"></span>
                        <input type="color" wire:model.live="settings.theme_color"
                               class="h-8 w-12 cursor-pointer rounded border border-gray-300 bg-white p-0">
                        <input type="text" wire:model.live="settings.theme_color"
                               class="w-24 rounded-lg border-gray-300 text-sm uppercase focus:border-emerald-500 focus:ring-emerald-500">
                    </label>

                    <div class="flex flex-wrap items-center gap-2">
                        @foreach (['#059669', '#2563eb', '#4f46e5', '#7c3aed', '#e11d48', '#ea580c', '#d97706', '#0891b2', '#65a30d', '#475569'] as $preset)
                            <button type="button" wire:click="$set('settings.theme_color', '{{ $preset }}')"
                                    class="h-8 w-8 rounded-full border-2 border-white shadow-md transition-transform hover:scale-110"
                                    style="background-color: {{ $preset }}"
                                    title="{{ $preset }}"></button>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-medium text-gray-500">{{ __('Preview') }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="rounded-lg px-4 py-2 text-sm font-semibold text-white shadow" style="background-color: {{ theme_color_ramp($settings['theme_color'])['600'] }}">{{ __('Primary button') }}</span>
                        <span class="rounded-lg px-4 py-2 text-sm font-medium" style="background-color: {{ theme_color_ramp($settings['theme_color'])['50'] }}; color: {{ theme_color_ramp($settings['theme_color'])['800'] }}">{{ __('Soft chip') }}</span>
                        <span class="rounded-lg px-4 py-2 text-sm font-medium" style="color: {{ theme_color_ramp($settings['theme_color'])['700'] }}">{{ __('Link') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Mail / SMTP') }}</h3>
                    <p class="mt-0.5 text-xs text-gray-500">{{ __('Configure your email server. It applies to all emails: verification, password reset and status notifications.') }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $settings['mail_enabled'] === '1' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $settings['mail_enabled'] === '1' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $settings['mail_enabled'] === '1' ? __('SMTP configured') : __('Mailer') . ': ' . config('mail.default') }}
                </span>
            </div>
            <div class="space-y-4 p-5">
                <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-3 text-sm text-gray-700" for="mail_enabled-{{ $this->getId() }}">
                    <input id="mail_enabled-{{ $this->getId() }}" name="mail_enabled" type="checkbox" wire:model="settings.mail_enabled" value="1"
                           class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="font-medium text-gray-900">{{ __('Use custom SMTP server') }}</span>
                        <span class="block text-xs text-gray-500">{{ __('If disabled, the server uses the .env mail configuration (log in development).') }}</span>
                    </span>
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_host-{{ $this->getId() }}">{{ __('SMTP Host') }}</label>
                        <input id="mail_host-{{ $this->getId() }}" name="mail_host" type="text" wire:model="settings.mail_host" placeholder="smtp.gmail.com"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_port-{{ $this->getId() }}">{{ __('SMTP Port') }}</label>
                        <input id="mail_port-{{ $this->getId() }}" name="mail_port" type="number" min="1" max="65535" wire:model="settings.mail_port" placeholder="587"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_username-{{ $this->getId() }}">{{ __('Username') }}</label>
                        <input id="mail_username-{{ $this->getId() }}" name="mail_username" type="text" wire:model="settings.mail_username" placeholder="no-reply@example.com"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_password-{{ $this->getId() }}">{{ __('Password') }}</label>
                        <input id="mail_password-{{ $this->getId() }}" name="mail_password" type="password" wire:model="settings.mail_password"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_encryption-{{ $this->getId() }}">{{ __('Encryption') }}</label>
                        <select id="mail_encryption-{{ $this->getId() }}" name="mail_encryption" wire:model="settings.mail_encryption"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="">None</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_from_address-{{ $this->getId() }}">{{ __('From Address') }}</label>
                        <input id="mail_from_address-{{ $this->getId() }}" name="mail_from_address" type="text" wire:model="settings.mail_from_address" placeholder="no-reply@example.com"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_from_name-{{ $this->getId() }}">{{ __('From Name') }}</label>
                        <input id="mail_from_name-{{ $this->getId() }}" name="mail_from_name" type="text" wire:model="settings.mail_from_name" placeholder="{{ __('Company Name') }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-medium text-gray-500">{{ __('Send a test email') }}</p>
                    <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input type="email" wire:model="testEmail" placeholder="you@example.com"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 sm:max-w-xs">
                        <button type="button" wire:click="sendTestEmail" class="btn-primary">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            {{ __('Send Test Email') }}
                        </button>
                    </div>
                    @error('testEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                {{ __('Save') }}
            </button>
        </div>
    </form>
</div>
