<div>
    <x-slot name="header">{{ __('Solicitud de Compra') }}</x-slot>

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-emerald-50/70 via-white to-gray-50/50 p-4 sm:p-6 lg:p-8">
        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12 lg:items-start">
            
            <!-- LEFT ON DESKTOP / SECOND ON MOBILE: QUICK QUOTE INFO -->
            <div class="order-2 lg:order-1 lg:col-span-5 flex flex-col justify-between">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 uppercase tracking-wide">
                        <i class="fa-solid fa-bolt text-xs text-emerald-600"></i>
                        {{ __('Quick Quote') }}
                    </span>

                    <h1 class="mt-3.5 text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
                        {{ __('Envíanos tu solicitud de compra') }}
                    </h1>

                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                        {{ __('Cuéntanos qué quieres y te cotizamos el costo total.') }}
                    </p>

                    <!-- Benefit bullets -->
                    <div class="mt-5 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-semibold text-gray-800">{{ __('Cotización gratis, sin compromiso') }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                <i class="fa-solid fa-link text-xs"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">{{ __('Envía el link del producto y nosotros nos encargamos del resto.') }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                <i class="fa-solid fa-tags text-xs"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">{{ __('Buscamos las mejores ofertas y descuentos para ti.') }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                <i class="fa-solid fa-bolt text-xs"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-semibold text-emerald-700">{{ __('Respuesta rápida por chat · Pago por adelantado') }}</span>
                        </div>
                    </div>

                    <!-- Consolidación de Paquetes Highlight Card -->
                    <div class="mt-5 rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-teal-50/60 to-white p-3.5 sm:p-4 shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-600/20">
                                <i class="fa-solid fa-boxes-packing text-base sm:text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-gray-900">{{ __('Consolidación de Paquetes') }}</h3>
                                <p class="text-[11px] sm:text-xs text-gray-600 mt-0.5">{{ __('Consolidamos todos tus paquetes en una sola caja.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3 Stats Metrics Row -->
                <div class="mt-6 pt-5 border-t border-gray-100 grid grid-cols-3 gap-2 sm:gap-3 text-center">
                    <div class="rounded-2xl border border-gray-100 bg-white p-2.5 sm:p-3 shadow-xs transition-colors hover:bg-emerald-50/60 hover:border-emerald-200">
                        <p class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight">12+</p>
                        <p class="text-[10px] sm:text-[11px] font-semibold text-gray-700 mt-0.5 sm:mt-1 leading-tight">{{ __('Países a los que enviamos') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-2.5 sm:p-3 shadow-xs transition-colors hover:bg-emerald-50/60 hover:border-emerald-200">
                        <p class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight">&lt; 1h</p>
                        <p class="text-[10px] sm:text-[11px] font-semibold text-gray-700 mt-0.5 sm:mt-1 leading-tight">{{ __('Respondemos en menos de 1 hora por chat.') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-2.5 sm:p-3 shadow-xs transition-colors hover:bg-emerald-50/60 hover:border-emerald-200">
                        <p class="text-xl sm:text-2xl font-black text-emerald-600 tracking-tight">100%</p>
                        <p class="text-[10px] sm:text-[11px] font-semibold text-gray-700 mt-0.5 sm:mt-1 leading-tight">{{ __('Pago por adelantado') }}</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT ON DESKTOP / FIRST ON MOBILE: FORM -->
            <div class="order-1 lg:order-2 lg:col-span-7">
                <div class="w-full rounded-3xl border border-gray-200 bg-white p-4 sm:p-6 lg:p-8 shadow-xl shadow-emerald-950/5 ring-1 ring-black/5">
                    <div class="border-b border-gray-100 pb-4 mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-clipboard-list text-emerald-600"></i>
                                {{ __('Solicitud de Compra') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Completa la siguiente información para cotizar y procesar tu pedido.') }}</p>
                        </div>
                        <a href="{{ route('portal.requests.index') }}" wire:navigate
                           class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-white hover:text-emerald-700 transition-colors shadow-2xs">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            <span>{{ __('Mis Pedidos') }}</span>
                        </a>
                    </div>
                    <livewire:client-registration-form />
                </div>
            </div>

        </div>
    </div>
</div>
