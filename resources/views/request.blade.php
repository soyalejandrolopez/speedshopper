<x-public-layout title="{{ __('Send us your purchase request') }}">
    <div class="relative overflow-hidden bg-gradient-to-b from-emerald-50/80 via-white to-gray-50/50 py-12 sm:py-16">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -start-32 top-0 h-96 w-96 rounded-full bg-emerald-100/80 blur-3xl"></div>
            <div class="absolute -end-24 bottom-0 h-96 w-96 rounded-full bg-teal-100/80 blur-3xl"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(5,150,105,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(5,150,105,0.04)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_70%_50%_at_50%_0%,black,transparent)]"></div>
        </div>

        <div class="relative mx-auto max-w-5xl px-4 sm:px-6">
            
            <!-- TOP HERO / QUICK QUOTE HEADER -->
            <div class="text-center max-w-3xl mx-auto" data-reveal>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-bold text-emerald-800 uppercase tracking-wide">
                    <i class="fa-solid fa-bolt text-xs text-emerald-600"></i>
                    Quick Quote
                </span>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                    {{ __('Envíanos tu solicitud de compra') }}
                </h1>

                <p class="mt-3 text-base text-gray-600 sm:text-lg leading-relaxed">
                    {{ __('Cuéntanos qué quieres y te cotizamos el costo total antes de comprar cualquier cosa.') }}
                </p>

                <!-- Value propositions / badges row -->
                <div class="mt-6 flex flex-wrap justify-center items-center gap-2.5 sm:gap-3 text-xs sm:text-sm font-medium text-gray-700">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3.5 py-1.5 shadow-sm border border-gray-200/80">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        {{ __('Cotización gratis, sin compromiso') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3.5 py-1.5 shadow-sm border border-gray-200/80">
                        <i class="fa-solid fa-link text-emerald-600"></i>
                        {{ __('Envía el link del producto y nosotros nos encargamos del resto.') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3.5 py-1.5 shadow-sm border border-gray-200/80">
                        <i class="fa-solid fa-tags text-emerald-600"></i>
                        {{ __('Buscamos las mejores ofertas y descuentos para ti.') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1.5 shadow-sm border border-emerald-200 text-emerald-800 font-semibold">
                        <i class="fa-solid fa-bolt text-emerald-600"></i>
                        {{ __('Respuesta rápida por chat · Sin pago por adelantado') }}
                    </span>
                </div>

                <!-- Consolidación de Paquetes Highlight Banner -->
                <div class="mt-6 mx-auto max-w-xl rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 via-teal-50/80 to-emerald-50 p-4 shadow-sm text-left sm:text-center">
                    <div class="flex items-center justify-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                            <i class="fa-solid fa-boxes-packing text-sm"></i>
                        </span>
                        <div>
                            <span class="font-bold text-gray-900 text-sm">{{ __('Consolidación de Paquetes') }}:</span>
                            <span class="text-gray-600 text-xs sm:text-sm ms-1">{{ __('Consolidamos todos tus paquetes en una sola caja.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- 3 Top Stats Metrics -->
                <div class="mt-6 max-w-2xl mx-auto grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-2xl border border-gray-200/70 bg-white p-3.5 shadow-sm transition-transform hover:-translate-y-0.5">
                        <p class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight">12+</p>
                        <p class="mt-1 text-[11px] sm:text-xs font-semibold text-gray-700 leading-tight">{{ __('Países a los que enviamos') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200/70 bg-white p-3.5 shadow-sm transition-transform hover:-translate-y-0.5">
                        <p class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight">&lt; 1h</p>
                        <p class="mt-1 text-[11px] sm:text-xs font-semibold text-gray-700 leading-tight">{{ __('Respondemos en menos de 1 hora por chat.') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200/70 bg-white p-3.5 shadow-sm transition-transform hover:-translate-y-0.5">
                        <p class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight">100%</p>
                        <p class="mt-1 text-[11px] sm:text-xs font-semibold text-gray-700 leading-tight">{{ __('Sin pago por adelantado') }}</p>
                    </div>
                </div>
            </div>

            <!-- REGISTRATION / PURCHASE REQUEST FORM -->
            <div class="mt-10 max-w-3xl mx-auto" data-reveal style="--reveal-delay: 120ms">
                <div class="w-full rounded-3xl border border-gray-200 bg-white p-6 shadow-xl shadow-emerald-950/5 ring-1 ring-black/5 sm:p-8">
                    <div class="border-b border-gray-100 pb-4 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-list text-emerald-600"></i>
                            {{ __('Client Registration') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Complete the following information to get started. This helps us identify your request and offer you the right service.') }}</p>
                    </div>
                    <livewire:client-registration-form />
                </div>
            </div>

        </div>
    </div>
</x-public-layout>
