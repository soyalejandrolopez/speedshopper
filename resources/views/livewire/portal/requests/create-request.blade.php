<div>
    <x-slot name="header">{{ __('Nueva Solicitud de Compra') }}</x-slot>

    <div class="mx-auto max-w-4xl space-y-6">
        {{-- Hero / Header Banner --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-600 to-teal-500 p-5 sm:p-6 text-white shadow-lg shadow-emerald-200">
            <div class="pointer-events-none absolute -end-10 -top-10 h-36 w-36 rounded-full bg-white/10 blur-xl"></div>
            <div class="relative flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-0.5 text-xs font-semibold text-emerald-50 backdrop-blur">
                        <i class="fa-solid fa-bolt text-emerald-300"></i>
                        {{ __('Cotización Rápida') }}
                    </span>
                    <h1 class="mt-2 text-xl sm:text-2xl font-bold">{{ __('¿Qué deseas comprar o enviar hoy?') }}</h1>
                    <p class="mt-1 text-xs sm:text-sm text-emerald-100">{{ __('Completa los datos de tu producto y nosotros nos encargamos de cotizar, comprar, recibir o reempacar.') }}</p>
                </div>
                <a href="{{ route('portal.requests.index') }}" wire:navigate
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur transition-all hover:bg-white/25">
                    <i class="fa-solid fa-arrow-left"></i>
                    {{ __('Volver a Mis Pedidos') }}
                </a>
            </div>
        </div>

        <form wire:submit="submit" class="space-y-6">
            {{-- 1. Selección de Servicios --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-xs">
                <div class="border-b border-gray-100 pb-3">
                    <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-100 text-xs font-black text-emerald-700">1</span>
                        {{ __('Selecciona el tipo de servicio') }}
                    </h2>
                    <p class="mt-0.5 text-xs text-gray-500">{{ __('Puedes elegir uno o varios servicios para tu solicitud.') }}</p>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    @foreach ($serviceDefinitions as $key => $svc)
                        <label class="relative flex flex-col justify-between rounded-xl border p-4 cursor-pointer transition-all duration-200 {{ in_array($key, $form['services'], true) ? 'border-emerald-500 bg-emerald-50/50 shadow-sm ring-2 ring-emerald-500/20' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50/50' }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ in_array($key, $form['services'], true) ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-200' : 'bg-gray-100 text-gray-600' }}">
                                    <i class="fa-solid {{ $svc['icon'] }} text-base"></i>
                                </div>
                                <input type="checkbox" wire:model.live="form.services" value="{{ $key }}" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            </div>
                            <div class="mt-3">
                                <p class="text-xs sm:text-sm font-bold text-gray-900">{{ $svc['title'] }}</p>
                                <p class="mt-1 text-[11px] text-gray-500 leading-snug">{{ $svc['subtitle'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('form.services') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- 2. Información del Producto --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-100 text-xs font-black text-emerald-700">2</span>
                        {{ __('Detalles del producto o compra') }}
                    </h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-semibold text-gray-700" for="product_name">{{ __('Nombre o descripción del producto') }} *</label>
                        <input id="product_name" type="text" wire:model="form.product_name"
                               placeholder="{{ __('Ej. Zapatos Nike Air Max 90, Laptop HP 15, Ropa de Shein...') }}"
                               class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        @error('form.product_name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-semibold text-gray-700" for="product_url">{{ __('Enlace web del producto (URL)') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 start-0 flex items-center ps-3.5 text-gray-400 pointer-events-none">
                                <i class="fa-solid fa-link text-xs"></i>
                            </span>
                            <input id="product_url" type="url" wire:model="form.product_url"
                                   placeholder="https://www.amazon.com/dp/... o https://us.shein.com/..."
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50/50 ps-9 pe-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        </div>
                        @error('form.product_url') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700" for="store">{{ __('Tienda / Web de compra') }}</label>
                        <input id="store" type="text" wire:model="form.store"
                               placeholder="{{ __('Ej. Amazon, Walmart, Shein, eBay, Nike...') }}"
                               class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700" for="size_color">{{ __('Talla / Color / Variante') }}</label>
                        <input id="size_color" type="text" wire:model="form.size_color"
                               placeholder="{{ __('Ej. Talla 9 US / Color Negro') }}"
                               class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700" for="quantity">{{ __('Cantidad') }} *</label>
                        <input id="quantity" type="number" min="1" wire:model="form.quantity"
                               class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        @error('form.quantity') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700" for="unit_price">{{ __('Presupuesto estimado (USD)') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 start-0 flex items-center ps-3.5 text-gray-400 font-bold text-xs pointer-events-none">$</span>
                            <input id="unit_price" type="number" step="0.01" min="0" wire:model="form.unit_price"
                                   placeholder="0.00"
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50/50 ps-7 pe-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        </div>
                    </div>
                </div>

                {{-- Additional checkboxes --}}
                <div class="pt-2 grid gap-3 sm:grid-cols-2">
                    <label class="flex items-center gap-2.5 text-xs text-gray-700 cursor-pointer">
                        <input type="checkbox" wire:model.live="form.find_deals" value="yes" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span>{{ __('Buscar las mejores ofertas o cupones de descuento.') }}</span>
                    </label>

                    <label class="flex items-center gap-2.5 text-xs text-gray-700 cursor-pointer">
                        <input type="checkbox" wire:model.live="form.already_purchased" value="yes" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span>{{ __('Ya realicé la compra por mi cuenta (solo recepción).') }}</span>
                    </label>
                </div>

                @if ($form['already_purchased'] === 'yes')
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 space-y-3">
                        <p class="text-xs font-bold text-emerald-900">{{ __('Datos del paquete comprado:') }}</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">{{ __('N° de Tracking / Rastreo') }}</label>
                                <input type="text" wire:model="form.tracking_number" placeholder="Ej. 1Z9999999999999999"
                                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">{{ __('N° de Orden de la Tienda') }}</label>
                                <input type="text" wire:model="form.order_number" placeholder="Ej. #112-3456789"
                                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 3. Reempaque y Cajas Heavy Duty (Opcional) --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-100 text-xs font-black text-emerald-700">3</span>
                            {{ __('Reempaque y Cajas Heavy Duty (Opcional)') }}
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500">{{ __('Agrega cajas para consolidar tus compras si lo requieres.') }}</p>
                    </div>
                    @if ($this->packagingTotal > 0)
                        <span class="rounded-xl bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                            {{ __('Total Cajas:') }} {{ money($this->packagingTotal) }}
                        </span>
                    @endif
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    {{-- Small --}}
                    <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50/50 p-3">
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ __('Caja Small') }}</p>
                            <p class="text-[11px] font-semibold text-emerald-600">{{ money($rates['box_small_heavy_duty'] ?? 15) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="decrementBox('small')" class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">-</button>
                            <span class="w-6 text-center font-bold text-xs">{{ $form['boxes_small'] }}</span>
                            <button type="button" wire:click="incrementBox('small')" class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">+</button>
                        </div>
                    </div>

                    {{-- Medium --}}
                    <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50/50 p-3">
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ __('Caja Mediana') }}</p>
                            <p class="text-[11px] font-semibold text-emerald-600">{{ money($rates['box_medium_heavy_duty'] ?? 20) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="decrementBox('medium')" class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">-</button>
                            <span class="w-6 text-center font-bold text-xs">{{ $form['boxes_medium'] }}</span>
                            <button type="button" wire:click="incrementBox('medium')" class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">+</button>
                        </div>
                    </div>

                    {{-- Large --}}
                    <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50/50 p-3">
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ __('Caja Larga') }}</p>
                            <p class="text-[11px] font-semibold text-emerald-600">{{ money($rates['box_large_heavy_duty'] ?? 25) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="decrementBox('large')" class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">-</button>
                            <span class="w-6 text-center font-bold text-xs">{{ $form['boxes_large'] }}</span>
                            <button type="button" wire:click="incrementBox('large')" class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">+</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Comentarios Adicionales --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-100 text-xs font-black text-emerald-700">4</span>
                        {{ __('Instrucciones o Comentarios') }}
                    </h2>
                </div>

                <div>
                    <textarea wire:model="form.comments" rows="3"
                              placeholder="{{ __('Cualquier detalle adicional que debamos tomar en cuenta (links extras, instrucciones de empaque, urgencia, etc.)...') }}"
                              class="w-full rounded-xl border border-gray-300 bg-gray-50/50 p-3.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"></textarea>
                </div>
            </div>

            {{-- Submit Row --}}
            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
                <a href="{{ route('portal.requests.index') }}" wire:navigate
                   class="w-full sm:w-auto text-center rounded-xl border border-gray-200/80 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-2xs hover:bg-gray-50">
                    {{ __('Cancelar') }}
                </a>

                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-600/20 transition-all hover:bg-emerald-700 hover:shadow-lg active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove><i class="fa-solid fa-paper-plane text-xs"></i></span>
                    <i wire:loading class="fa-solid fa-spinner fa-spin text-xs"></i>
                    <span>{{ __('Enviar Solicitud de Compra') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
