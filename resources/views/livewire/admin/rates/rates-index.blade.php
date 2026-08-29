<div>
    <x-slot name="header">{{ __('Rate Sheet & Pricing PDF') }}</x-slot>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
            <div class="border-b border-gray-100 pb-5">
                <h2 class="text-lg font-bold text-gray-900">{{ __('Rate Sheet & Pricing PDF') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500">
                    {{ __('Configure personal shopper tiers, heavy duty boxes, logistics fees, and bilingual notes for the official rate sheet.') }}
                </p>
            </div>

            <div class="mt-6 space-y-6">
                {{-- 1. Personal Shopper Tiers --}}
                <div>
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-100 text-xs font-extrabold text-emerald-800">1</span>
                        {{ __('Personal Shopper Tiers') }}
                    </h3>
                    <div class="mt-3 space-y-3">
                        @foreach ($rates['shopper_tiers'] ?? [] as $index => $tier)
                            <div class="grid gap-3 rounded-xl border border-gray-200/80 bg-gray-50/60 p-3.5 sm:grid-cols-5">
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Min ($)') }}</label>
                                    <input type="number" min="0" wire:model="rates.shopper_tiers.{{ $index }}.min"
                                           class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Max ($ or empty for +)') }}</label>
                                    <input type="number" min="0" wire:model="rates.shopper_tiers.{{ $index }}.max" placeholder="∞"
                                           class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Commission (%)') }}</label>
                                    <input type="number" step="0.5" wire:model="rates.shopper_tiers.{{ $index }}.percent"
                                           class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Stores Included') }}</label>
                                    <input type="number" min="1" wire:model="rates.shopper_tiers.{{ $index }}.stores"
                                           class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Hours Included') }}</label>
                                    <input type="number" min="1" wire:model="rates.shopper_tiers.{{ $index }}.hours"
                                           class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Additional Store Visit Fee ($ USD)') }}</label>
                            <input type="number" step="0.01" min="0" wire:model="rates.extra_store_fee"
                                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                {{-- 2. Compras Online & Servicios de Almacén --}}
                <div class="border-t border-gray-100 pt-5">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-100 text-xs font-extrabold text-blue-800">2</span>
                        {{ __('Compras Online (Reempaque / Almacén)') }}
                    </h3>
                    <div class="mt-3 grid gap-3 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Comisión Almacén (%)') }}</label>
                            <input type="number" step="0.5" min="0" max="100" wire:model="rates.warehouse_percent"
                                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Servicio de Traslado de Caja al Almacén ($)') }}</label>
                            <input type="number" step="0.01" min="0" wire:model="rates.warehouse_delivery_fee"
                                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Almacenaje mensual tras 30 días ($/mes)') }}</label>
                            <input type="number" step="0.01" min="0" wire:model="rates.monthly_storage_fee"
                                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                {{-- 3. Precios de los Reempaques (Cajas Heavy Duty) --}}
                <div class="border-t border-gray-100 pt-5">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-teal-100 text-xs font-extrabold text-teal-800">3</span>
                        {{ __('Precios de los Reempaques (Cajas Heavy Duty)') }}
                    </h3>
                    <div class="mt-3 grid gap-3 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('1 Caja Small Heavy Duty ($)') }}</label>
                            <input type="number" step="0.01" min="0" wire:model="rates.box_small_heavy_duty"
                                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('1 Caja Mediana Heavy Duty ($)') }}</label>
                            <input type="number" step="0.01" min="0" wire:model="rates.box_medium_heavy_duty"
                                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('1 Caja Larga Heavy Duty ($)') }}</label>
                            <input type="number" step="0.01" min="0" wire:model="rates.box_large_heavy_duty"
                                   class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                {{-- 4. Descriptive Notes & Policy (Bilingual) --}}
                <div class="border-t border-gray-100 pt-5">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-purple-100 text-xs font-extrabold text-purple-800">4</span>
                        {{ __('Descriptive Notes & Policy (Bilingual)') }}
                    </h3>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Online Repackage Notice (Spanish)') }}</label>
                            <textarea wire:model="rates.notes_es.repackage_notice" rows="2"
                                      class="w-full rounded-lg border border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Online Repackage Notice (English)') }}</label>
                            <textarea wire:model="rates.notes_en.repackage_notice" rows="2"
                                      class="w-full rounded-lg border border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Storage Policy Notice (Spanish)') }}</label>
                            <textarea wire:model="rates.notes_es.storage_notice" rows="2"
                                      class="w-full rounded-lg border border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Storage Policy Notice (English)') }}</label>
                            <textarea wire:model="rates.notes_en.storage_notice" rows="2"
                                      class="w-full rounded-lg border border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end border-t border-gray-100 pt-4">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>{{ __('Guardar') }}</span>
                </button>
            </div>
        </div>
    </form>

    {{-- Send PDF via Email Modal --}}
    @if ($showSendModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeSendModal"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl"
                 x-on:keydown.escape.window="$wire.closeSendModal()">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2 text-gray-900">
                        <i class="fa-solid fa-envelope text-emerald-600"></i>
                        <h3 class="text-base font-bold">{{ __('Send Rate Sheet PDF by Email') }}</h3>
                    </div>
                    <button type="button" wire:click="closeSendModal" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form wire:submit="sendRatesEmail" class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700">{{ __('Recipient Email Address') }} *</label>
                        <input type="email" wire:model="recipientEmail" placeholder="cliente@example.com"
                               class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                        @error('recipientEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700">{{ __('PDF Language') }} *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 text-xs font-medium transition-all {{ $emailLocale === 'es' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-500/20' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}">
                                <input type="radio" wire:model="emailLocale" value="es" class="text-emerald-600 focus:ring-emerald-500">
                                <span>🇪🇸 Español (Tarifario Oficial)</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 text-xs font-medium transition-all {{ $emailLocale === 'en' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-500/20' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}">
                                <input type="radio" wire:model="emailLocale" value="en" class="text-emerald-600 focus:ring-emerald-500">
                                <span>🇺🇸 English (Rate Sheet)</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700">{{ __('Optional Custom Note') }}</label>
                        <textarea wire:model="customEmailNote" rows="2" placeholder="{{ __('e.g. Adjunto encontrarás nuestra lista de precios actualizada.') }}"
                                  class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea>
                    </div>

                    <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50/70 p-3 text-xs text-emerald-800">
                        <i class="fa-solid fa-shield text-emerald-600 shrink-0"></i>
                        <span>
                            {{ __('Automatic admin copy will be sent to:') }}
                            <strong>{{ \App\Models\Setting::get('admin_notification_email') ?: __('Not configured (will only send to recipient)') }}</strong>
                        </span>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-2 border-t border-gray-100 pt-4">
                        <button type="button" wire:click="closeSendModal"
                                class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            {{ __('Send Email') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
