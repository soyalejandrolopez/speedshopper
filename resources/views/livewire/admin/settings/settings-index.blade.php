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
                    <input id="company_name-{{ $this->getId() }}" name="company_name" type="text" wire:model="settings.company_name" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="currency-{{ $this->getId() }}">{{ __('Currency') }}</label>
                    <input id="currency-{{ $this->getId() }}" name="currency" type="text" wire:model="settings.currency" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="warehouse_address-{{ $this->getId() }}">{{ __('Warehouse Address') }}</label>
                    <input id="warehouse_address-{{ $this->getId() }}" name="warehouse_address" type="text" wire:model="settings.warehouse_address" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="whatsapp_phone-{{ $this->getId() }}">{{ __('WhatsApp Phone') }}</label>
                    <input id="whatsapp_phone-{{ $this->getId() }}" name="whatsapp_phone" type="text" wire:model="settings.whatsapp_phone" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="countries_served-{{ $this->getId() }}">{{ __('Countries Served (ISO2)') }}</label>
                    <input id="countries_served-{{ $this->getId() }}" name="countries_served" type="text" wire:model="settings.countries_served" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                    <input id="shopper_fee-{{ $this->getId() }}" name="shopper_fee" type="number" step="0.01" min="0" wire:model="settings.shopper_fee" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                    <input id="receiving_fee-{{ $this->getId() }}" name="receiving_fee" type="number" step="0.01" min="0" wire:model="settings.receiving_fee" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="packing_fee-{{ $this->getId() }}">{{ __('Packing Fee') }}</label>
                    <input id="packing_fee-{{ $this->getId() }}" name="packing_fee" type="number" step="0.01" min="0" wire:model="settings.packing_fee" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="admin_notification_email-{{ $this->getId() }}">
                        {{ __('Correo para Notificaciones de Administrador') }}
                    </label>
                    <input id="admin_notification_email-{{ $this->getId() }}" name="admin_notification_email" type="text" wire:model="settings.admin_notification_email"
                           placeholder="admin@speedshopper.com" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <p class="mt-1 text-xs text-gray-400">{{ __('Recibirá un correo instantáneo cuando se envíe una nueva solicitud de compra o mensaje de contacto. Puedes separar varios correos por coma.') }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="whatsapp_api_url-{{ $this->getId() }}">{{ __('WhatsApp API URL') }}</label>
                    <input id="whatsapp_api_url-{{ $this->getId() }}" name="whatsapp_api_url" type="text" wire:model="settings.whatsapp_api_url"
                           placeholder="https://..." class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <p class="mt-1 text-xs text-gray-400">{{ __('Receives a POST with phone and message. Leave empty to skip WhatsApp.') }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="whatsapp_api_token-{{ $this->getId() }}">{{ __('WhatsApp API Token') }}</label>
                    <input id="whatsapp_api_token-{{ $this->getId() }}" name="whatsapp_api_token" type="password" wire:model="settings.whatsapp_api_token"
                           class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                                <button type="button" @click="swalConfirmDelete(() => $wire.removeLogo())"
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
                                <i class="fa-solid fa-bag-shopping text-3xl text-gray-300"></i>
                            @endif
                        </span>
                        <div class="flex-1 space-y-2">
                            <input type="file" wire:model="favicon" accept="image/x-icon,.png,.svg,.webp,.ico"
                                   class="block w-full text-sm text-gray-500 file:me-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                            @error('favicon') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            @if (brand_favicon_url())
                                <button type="button" @click="swalConfirmDelete(() => $wire.removeFavicon())"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700">
                                    {{ __('Remove Favicon') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{
                activeColor: @entangle('settings.theme_color').live,
                applyLiveTheme(hex) {
                    if (!hex || typeof hex !== 'string') return;
                    hex = hex.trim().replace(/^#/, '');
                    if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                    if (hex.length !== 6 || !/^[0-9a-fA-F]{6}$/.test(hex)) return;

                    const r = parseInt(hex.substring(0, 2), 16);
                    const g = parseInt(hex.substring(2, 4), 16);
                    const b = parseInt(hex.substring(4, 6), 16);

                    const lighter = { 50: 0.90, 100: 0.80, 200: 0.66, 300: 0.50, 400: 0.33 };
                    const darker  = { 600: 0.22, 700: 0.38, 800: 0.52, 900: 0.64, 950: 0.80 };

                    const toHex = (n) => ('0' + Math.max(0, Math.min(255, Math.round(n))).toString(16)).slice(-2);
                    const ramp = { '500': '#' + hex.toLowerCase() };

                    for (const [shade, w] of Object.entries(lighter)) {
                        ramp[shade] = '#' + toHex(r + (255 - r) * w) + toHex(g + (255 - g) * w) + toHex(b + (255 - b) * w);
                    }
                    for (const [shade, k] of Object.entries(darker)) {
                        ramp[shade] = '#' + toHex(r * (1 - k)) + toHex(g * (1 - k)) + toHex(b * (1 - k));
                    }

                    let styleTag = document.getElementById('site-theme-override');
                    if (!styleTag) {
                        styleTag = document.createElement('style');
                        styleTag.id = 'site-theme-override';
                        document.head.appendChild(styleTag);
                    }

                    let css = `--theme-color: ${ramp['500']}; --theme-primary: ${ramp['600']}; --theme-primary-hover: ${ramp['700']}; --theme-primary-light: ${ramp['50']}; `;
                    ['emerald', 'teal', 'brand'].forEach(family => {
                        for (const [shade, val] of Object.entries(ramp)) {
                            css += `--color-${family}-${shade}: ${val}; `;
                        }
                    });

                    styleTag.textContent = `:root { ${css} }`;

                    let meta = document.querySelector('meta[name="theme-color-custom"]');
                    if (meta) {
                        meta.content = css;
                    }
                }
            }"
            x-init="applyLiveTheme(activeColor); $watch('activeColor', val => applyLiveTheme(val))"
            class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Site Theme') }}</h3>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Pick the main color of the website, portal and reports. It applies everywhere automatically.') }}</p>
            </div>
            <div class="space-y-4 p-5">
                <div class="flex flex-wrap items-center gap-3">
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">
                        <span class="h-8 w-8 rounded-lg border border-gray-300 shadow-inner" style="background-color: {{ $settings['theme_color'] }}"></span>
                        <input type="color" wire:model.live="settings.theme_color"
                               @change="$wire.saveThemeColor(activeColor)"
                               class="h-8 w-12 cursor-pointer rounded border border-gray-300 bg-white p-0">
                        <input type="text" wire:model.live="settings.theme_color"
                               @change="$wire.saveThemeColor(activeColor)"
                               class="w-24 rounded-lg border border-gray-300 text-sm uppercase focus:border-emerald-500 focus:ring-emerald-500">
                    </label>

                    <button type="button" wire:click="saveThemeColor"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-emerald-700 active:scale-95">
                        <i class="fa-solid fa-palette text-xs"></i>
                        {{ __('Guardar y Aplicar Color') }}
                    </button>

                    <div class="flex flex-wrap items-center gap-2">
                        @foreach (['#d86ec1', '#670753', '#059669', '#2563eb', '#4f46e5', '#7c3aed', '#e11d48', '#ea580c', '#d97706', '#0891b2', '#65a30d', '#475569'] as $preset)
                            <button type="button"
                                    @click="activeColor = '{{ $preset }}'"
                                    wire:click="saveThemeColor('{{ $preset }}')"
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
                               class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_port-{{ $this->getId() }}">{{ __('SMTP Port') }}</label>
                        <input id="mail_port-{{ $this->getId() }}" name="mail_port" type="number" min="1" max="65535" wire:model="settings.mail_port" placeholder="587"
                               class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_username-{{ $this->getId() }}">{{ __('Username') }}</label>
                        <input id="mail_username-{{ $this->getId() }}" name="mail_username" type="text" wire:model="settings.mail_username" placeholder="no-reply@example.com"
                               class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_password-{{ $this->getId() }}">{{ __('Password') }}</label>
                        <input id="mail_password-{{ $this->getId() }}" name="mail_password" type="password" wire:model="settings.mail_password"
                               class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_encryption-{{ $this->getId() }}">{{ __('Encryption') }}</label>
                        <select id="mail_encryption-{{ $this->getId() }}" name="mail_encryption" wire:model="settings.mail_encryption"
                                class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="">{{ __('None') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_from_address-{{ $this->getId() }}">{{ __('From Address') }}</label>
                        <input id="mail_from_address-{{ $this->getId() }}" name="mail_from_address" type="text" wire:model="settings.mail_from_address" placeholder="no-reply@example.com"
                               class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="mail_from_name-{{ $this->getId() }}">{{ __('From Name') }}</label>
                        <input id="mail_from_name-{{ $this->getId() }}" name="mail_from_name" type="text" wire:model="settings.mail_from_name" placeholder="{{ __('Company Name') }}"
                               class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-medium text-gray-500">{{ __('Send a test email') }}</p>
                    <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input type="email" wire:model="testEmail" placeholder="you@example.com"
                                class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 sm:max-w-xs">
                        <button type="button" wire:click="sendTestEmail" class="btn-primary">
                            <i class="fa-solid fa-envelope text-base"></i>
                            {{ __('Send Test Email') }}
                        </button>
                    </div>
                    @error('testEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    @if ($mailTestStatus === 'sent')
                        <div class="mt-2 flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                            <i class="fa-solid fa-check text-base"></i>
                            {{ $mailTestMessage }}
                        </div>
                    @elseif ($mailTestStatus === 'error')
                        <div class="mt-2 flex items-start gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700 ring-1 ring-inset ring-red-200">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-base shrink-0"></i>
                            <span class="break-all">{{ $mailTestMessage }}</span>
                        </div>
                    @endif
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
