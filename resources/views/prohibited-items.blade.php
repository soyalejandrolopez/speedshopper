<x-public-layout :title="__('Productos Prohibidos y Restringidos')">
    <!-- HERO SECTION -->
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-50/70 via-white to-gray-50/50 py-8 sm:py-14">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -start-24 top-0 h-80 w-80 rounded-full bg-rose-100/60 blur-3xl"></div>
            <div class="absolute -end-24 bottom-0 h-80 w-80 rounded-full bg-emerald-100/60 blur-3xl"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(5,150,105,0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(5,150,105,0.03)_1px,transparent_1px)] bg-[size:48px_48px] [mask-image:radial-gradient(ellipse_70%_50%_at_50%_0%,black,transparent)]"></div>
        </div>

        <div class="relative mx-auto max-w-4xl px-3.5 text-center sm:px-6">
            <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3.5 py-1.5 text-xs font-semibold text-rose-700 shadow-sm">
                <i class="fa-solid fa-shield-halved text-rose-600 text-xs"></i>
                {{ __('Políticas de Envío y Seguridad') }}
            </span>

            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                {{ __('Productos Prohibidos y Restringidos') }}
            </h1>

            <p class="mt-3 text-sm sm:text-base leading-relaxed text-gray-600">
                {{ __('Por razones de seguridad y regulaciones de transporte, algunos productos no pueden ser recibidos, almacenados o enviados a destinos internacionales.') }}
            </p>
        </div>
    </section>

    <!-- CONTENT SECTION -->
    <section class="mx-auto max-w-5xl px-3.5 py-6 sm:px-6 sm:py-8 pb-20">
        <div class="grid gap-8 lg:grid-cols-2">

            <!-- PROHIBITED ITEMS CARD (RED ACCENT) -->
            <div class="overflow-hidden rounded-3xl border border-rose-200/80 bg-white shadow-xl shadow-rose-100/40">
                <div class="border-b border-rose-100 bg-gradient-to-r from-rose-600 to-red-600 px-6 py-5 text-white">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur">
                            <i class="fa-solid fa-ban text-xl"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-bold">{{ __('No aceptamos:') }}</h2>
                            <p class="text-xs text-rose-100">{{ __('Artículos terminantemente prohibidos') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @php
                        $prohibited = [
                            ['icon' => 'fa-solid fa-person-rifle', 'text' => __('Armas de fuego, municiones, explosivos o artículos relacionados.')],
                            ['icon' => 'fa-solid fa-cannabis', 'text' => __('Drogas ilegales, marihuana, THC o productos con CBD.')],
                            ['icon' => 'fa-solid fa-wine-bottle', 'text' => __('Bebidas alcohólicas.')],
                            ['icon' => 'fa-solid fa-smoking', 'text' => __('Cigarrillos, tabaco, vapes o cigarrillos electrónicos.')],
                            ['icon' => 'fa-solid fa-gas-pump', 'text' => __('Gasolina, combustibles y productos altamente inflamables.')],
                            ['icon' => 'fa-solid fa-biohazard', 'text' => __('Sustancias químicas peligrosas, tóxicas o corrosivas.')],
                            ['icon' => 'fa-solid fa-money-bill-1-wave', 'text' => __('Dinero en efectivo.')],
                            ['icon' => 'fa-solid fa-copyright', 'text' => __('Productos falsificados o imitaciones que infrinjan marcas registradas.')],
                            ['icon' => 'fa-solid fa-paw', 'text' => __('Animales vivos, restos humanos o materiales biológicos peligrosos.')],
                            ['icon' => 'fa-solid fa-gavel', 'text' => __('Cualquier producto cuya compra, posesión, exportación o importación sea ilegal.')],
                        ];
                    @endphp

                    <ul class="space-y-3.5">
                        @foreach ($prohibited as $item)
                            <li class="flex items-start gap-3.5 rounded-xl border border-rose-50 bg-rose-50/40 p-3 transition-colors hover:bg-rose-50/70">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                                    <i class="{{ $item['icon'] }} text-sm"></i>
                                </span>
                                <span class="text-sm font-medium text-gray-800 leading-snug pt-0.5">
                                    {{ $item['text'] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- RESTRICTED ITEMS CARD (AMBER ACCENT) -->
            <div class="overflow-hidden rounded-3xl border border-amber-200/80 bg-white shadow-xl shadow-amber-100/40 flex flex-col justify-between">
                <div>
                    <div class="border-b border-amber-100 bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-5 text-white">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur">
                                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            </span>
                            <div>
                                <h2 class="text-xl font-bold">{{ __('Productos que requieren revisión antes de comprar o enviar:') }}</h2>
                                <p class="text-xs text-amber-100">{{ __('Consulta previa obligatoria con nosotros') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="mb-4 text-xs font-semibold text-amber-800 bg-amber-50 rounded-lg p-3 border border-amber-200/60">
                            <i class="fa-solid fa-circle-info text-amber-600 me-1"></i>
                            {{ __('Antes de realizar la compra, comunícate con nosotros si tu pedido contiene:') }}
                        </p>

                        @php
                            $restricted = [
                                ['icon' => 'fa-solid fa-spray-can-sparkles', 'text' => __('Perfumes o fragancias.')],
                                ['icon' => 'fa-solid fa-wand-magic-sparkles', 'text' => __('Esmaltes de uñas.')],
                                ['icon' => 'fa-solid fa-spray-can', 'text' => __('Aerosoles o productos en spray.')],
                                ['icon' => 'fa-solid fa-pump-soap', 'text' => __('Cosméticos o líquidos.')],
                                ['icon' => 'fa-solid fa-battery-half', 'text' => __('Baterías de litio, power banks o equipos que contengan baterías.')],
                                ['icon' => 'fa-solid fa-capsules', 'text' => __('Medicamentos, vitaminas o suplementos.')],
                                ['icon' => 'fa-solid fa-utensils', 'text' => __('Alimentos.')],
                                ['icon' => 'fa-solid fa-seedling', 'text' => __('Plantas, semillas o productos agrícolas.')],
                                ['icon' => 'fa-solid fa-gem', 'text' => __('Joyería o artículos de alto valor.')],
                                ['icon' => 'fa-solid fa-laptop', 'text' => __('Equipos electrónicos.')],
                            ];
                        @endphp

                        <ul class="space-y-3">
                            @foreach ($restricted as $item)
                                <li class="flex items-start gap-3 rounded-xl border border-amber-50 bg-amber-50/30 p-2.5 transition-colors hover:bg-amber-50/60">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                        <i class="{{ $item['icon'] }} text-xs"></i>
                                    </span>
                                    <span class="text-sm font-medium text-gray-800 leading-snug pt-0.5">
                                        {{ $item['text'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="border-t border-amber-100 bg-amber-50/50 p-5">
                    <p class="text-xs text-gray-600 leading-relaxed">
                        <i class="fa-solid fa-plane text-amber-600 me-1"></i>
                        {{ __('La aceptación de estos productos dependerá de las regulaciones de la compañía de transporte y del país de destino.') }}
                    </p>
                </div>
            </div>

        </div>

        <!-- IMPORTANT NOTICE CARD -->
        <div class="mt-12 overflow-hidden rounded-3xl border border-emerald-200/90 bg-gradient-to-br from-emerald-50/80 via-white to-white p-6 sm:p-8 shadow-xl shadow-emerald-100/50">
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-200">
                    <i class="fa-solid fa-circle-exclamation text-2xl"></i>
                </span>
                <div class="flex-1">
                    <h3 class="text-xl font-extrabold text-gray-900">{{ __('Importante') }}</h3>
                    
                    <div class="mt-4 space-y-3.5 text-sm leading-relaxed text-gray-700">
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 mt-1 text-sm shrink-0"></i>
                            <p>{{ __('Cada país tiene sus propias regulaciones de importación. Antes de realizar una compra, recomendamos consultar con nosotros si tienes dudas sobre algún producto.') }}</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 mt-1 text-sm shrink-0"></i>
                            <p>{{ __('No realices compras de productos restringidos utilizando nuestra dirección sin autorización previa.') }}</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 mt-1 text-sm shrink-0"></i>
                            <p>{{ __('Los productos que no puedan ser enviados podrán estar sujetos a devolución al vendedor, y los gastos asociados serán responsabilidad del cliente.') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-4 pt-2">
                        @php
                            $waPhone = preg_replace('/\D+/', '', \App\Models\Setting::get('whatsapp_phone', '13462333199'));
                            $waMsg = urlencode(__('¡Hola! Quisiera consultar si un producto específico puede ser transportado a mi país.'));
                        @endphp
                        <a href="https://wa.me/{{ !empty($waPhone) ? $waPhone : '13462333199' }}?text={{ $waMsg }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-primary inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] hover:shadow-lg">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            {{ __('Consultar Producto por WhatsApp') }}
                        </a>

                        <a href="{{ route('contact') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:border-emerald-300 hover:bg-emerald-50/40 hover:text-emerald-700"
                           wire:navigate>
                            <i class="fa-solid fa-envelope text-base text-gray-500"></i>
                            {{ __('Contactar por Formulario') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
