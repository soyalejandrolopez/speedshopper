<x-public-layout>
    @php
        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => \App\Models\Setting::get('company_name', config('app.name')),
            'description' => __('Personal shopper en Baytown, TX: compramos, recibimos y enviamos tus compras de EE. UU. a Latinoamérica.'),
            'url' => url('/'),
            'telephone' => \App\Models\Setting::get('whatsapp_phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => \App\Models\Setting::get('warehouse_address'),
                'addressLocality' => 'Baytown',
                'addressRegion' => 'TX',
                'addressCountry' => 'US',
            ],
            'areaServed' => 'Latin America',
            'priceRange' => '$$',
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <section class="gradient-hero relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="{{ __('A couple shopping with bags from the United States') }}"
                 class="h-full w-full object-cover object-center" fetchpriority="high">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/35 to-black/15"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/25"></div>
        </div>

        <div class="relative mx-auto max-w-4xl px-4 py-16 text-center sm:px-6 lg:py-24">
            <div class="flex flex-col items-center" data-reveal>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-black/20 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                    </span>
                    {{ __('Personal Shopper in Baytown, TX') }}
                </span>

                <h1 class="mt-6 text-4xl font-extrabold leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-6xl">
                    {{ __('We buy, receive and ship') }}
                    <span class="text-white">{{ __('your products to Latin America') }}</span>
                </h1>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('request') }}" class="btn-primary px-6 py-3 text-base">
                        {{ __('Request a Purchase') }}
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/40 bg-white/10 px-6 py-3 text-base font-semibold text-white backdrop-blur transition-all duration-200 hover:bg-white/20">
                            {{ __('Create Account') }}
                        </a>
                    @endguest
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-medium text-gray-200" data-reveal>
                    <span class="inline-flex items-center gap-1.5">
<svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Zelle
                    </span>
                    <span class="inline-flex items-center gap-1.5">
<svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        PayPal
                    </span>
                    <span class="inline-flex items-center gap-1.5">
<svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Card') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
<svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Bank transfer') }}
                    </span>
                </div>

                <div class="mt-12 grid max-w-md grid-cols-3 gap-4 border-t border-white/20 pt-6 text-center">
                    @php
                        $fee = (float) \App\Models\Setting::get('shopper_fee', '10');
                        $pct = (bool) \App\Models\Setting::get('shopper_fee_is_percent', '0');
                        $countries = count(array_filter(array_map('trim', explode(',', \App\Models\Setting::get('countries_served', 'MX,GT')))));
                    @endphp
                    <div>
                        <p class="text-2xl font-bold text-white sm:text-3xl">
                            <span data-count="{{ $pct ? $fee : $fee }}" data-prefix="{{ $pct ? '' : '$' }}">{{ $pct ? $fee : money($fee) }}</span>{{ $pct ? '%' : '' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-300">{{ __('Shopper Fee (per order)') }}</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white sm:text-3xl">
                            <span data-count="{{ (float) \App\Models\Setting::get('receiving_fee', '2.50') }}" data-prefix="$">$0.00</span>
                        </p>
                        <p class="mt-1 text-xs text-gray-300">{{ __('Receiving Fee (per package)') }}</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white sm:text-3xl">
                            <span data-count="{{ $countries }}">{{ $countries }}</span>+
                        </p>
                        <p class="mt-1 text-xs text-gray-300">{{ __('Countries We Ship To') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="text-center" data-reveal>
            <span class="section-eyebrow">{{ __('How it works') }}</span>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Fácil, rápido y confiable. Nosotros nos encargamos del proceso por ti.') }}</h2>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-3">
            @php
                $steps = [
                    ['title' => __('Envía tu pedido'), 'text' => __('Compártenos el link, foto o nombre del producto que deseas. También podemos ayudarte a encontrar ofertas y mejores precios.'), 'icon' => 'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244', 'color' => 'bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white'],
                    ['title' => __('Compramos o recibimos tu paquete'), 'text' => __('Hacemos la compra por ti, o recibimos en Baytown, Texas, los paquetes que compres online en tus tiendas favoritas.'), 'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', 'color' => 'bg-teal-100 text-teal-600 group-hover:bg-teal-600 group-hover:text-white'],
                    ['title' => __('Empacamos y enviamos'), 'text' => __('Organizamos, empacamos y enviamos tus productos a Latinoamérica de forma segura, con seguimiento de tu envío.'), 'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12', 'color' => 'bg-sky-100 text-sky-600 group-hover:bg-sky-600 group-hover:text-white'],
                ];
            @endphp

            @foreach ($steps as $index => $step)
                <div class="card card-hover group relative p-6 pt-10 text-center" data-reveal style="--reveal-delay: {{ $index * 100 }}ms">
                    <span class="absolute top-4 end-4 flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-400">{{ $index + 1 }}</span>
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl transition-all duration-300 {{ $step['color'] }} group-hover:scale-110 group-hover:rotate-3">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $step['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-semibold text-gray-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-500">{{ $step['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-gradient-to-b from-white to-emerald-50/50 py-20" id="fees">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="text-center" data-reveal>
                <span class="section-eyebrow">{{ __('Costos y Fees') }}</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Trabajamos con tarifas claras para que conozcas el costo de nuestro servicio antes de procesar tu compra o envío.') }}</h2>
            </div>
            
            <div class="mt-16 grid gap-8 lg:grid-cols-2">
                <div class="card p-8" data-reveal>
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" /></svg>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('Compras Personalizadas en Tiendas') }}</h3>
                    </div>
                    <div class="mt-6 space-y-5">
                        <div class="rounded-xl bg-gray-50 p-4 transition-colors hover:bg-emerald-50">
                            <div class="flex items-center justify-between font-semibold text-gray-900">
                                <span>{{ __('Compras de $100 a $699') }}</span>
                                <span class="text-emerald-600">{{ __('Fee: 20%') }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Incluye hasta 2 tiendas y 2 horas de servicio.') }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4 transition-colors hover:bg-emerald-50">
                            <div class="flex items-center justify-between font-semibold text-gray-900">
                                <span>{{ __('Compras de $700 a $1,499') }}</span>
                                <span class="text-emerald-600">{{ __('Fee: 15%') }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Incluye hasta 3 tiendas y 3 horas de servicio.') }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4 transition-colors hover:bg-emerald-50">
                            <div class="flex items-center justify-between font-semibold text-gray-900">
                                <span>{{ __('Compras de $1,500 o más') }}</span>
                                <span class="text-emerald-600">{{ __('Fee: 15%') }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Incluye hasta 4 tiendas y 4 horas de servicio.') }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-700">{{ __('Tienda adicional:') }} <span class="font-normal text-gray-500">{{ __('$20 por cada tienda adicional.') }}</span></p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="card p-8" data-reveal style="--reveal-delay: 100ms">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-100 text-teal-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                            </span>
                            <h3 class="text-xl font-bold text-gray-900">{{ __('Compras Online') }}</h3>
                        </div>
                        <div class="mt-6 space-y-3 text-sm text-gray-600">
                            <p>{{ __('Si realizamos tus compras por internet por ti:') }}</p>
                            <p class="font-semibold text-emerald-700">{{ __('Fee de servicio: 15% del total de la compra online.') }}</p>
                            <p class="text-gray-500">{{ __('Este fee corresponde al servicio de procesamiento, coordinación y seguimiento de tu compra.') }}</p>
                        </div>
                    </div>

                    <div class="card p-8" data-reveal style="--reveal-delay: 200ms">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                            </span>
                            <h3 class="text-xl font-bold text-gray-900">{{ __('Servicio de Embalaje') }}</h3>
                        </div>
                        <div class="mt-6">
                            <p class="mb-4 text-sm text-gray-500">{{ __('El costo del embalaje dependerá del tamaño de la caja utilizada:') }}</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="rounded-lg bg-gray-50 p-3 text-center transition-colors hover:bg-sky-50">
                                    <div class="text-xs font-semibold uppercase text-gray-500">{{ __('Small') }}</div>
                                    <div class="mt-1 text-lg font-bold text-gray-900">$15</div>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3 text-center transition-colors hover:bg-sky-50">
                                    <div class="text-xs font-semibold uppercase text-gray-500">{{ __('Mediana') }}</div>
                                    <div class="mt-1 text-lg font-bold text-gray-900">$20</div>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3 text-center transition-colors hover:bg-sky-50">
                                    <div class="text-xs font-semibold uppercase text-gray-500">{{ __('Large') }}</div>
                                    <div class="mt-1 text-lg font-bold text-gray-900">$25</div>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-500">{{ __('El servicio incluye la preparación y organización de tus productos para su envío.') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="card p-8 lg:col-span-2" data-reveal>
                    <div class="grid gap-8 sm:grid-cols-2">
                        <div>
                            <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                                </span>
                                <h3 class="text-xl font-bold text-gray-900">{{ __('Entrega a Envío') }}</h3>
                            </div>
                            <p class="mt-4 text-sm text-gray-600">{{ __('Podemos llevar tu caja preparada a la compañía de envío seleccionada.') }}</p>
                            <p class="mt-2 text-2xl font-bold text-emerald-600">20 USD</p>
                            <div class="mt-4 overflow-hidden rounded-xl border border-gray-100">
                                <img src="{{ asset('images/entrega-envio.jpg') }}" alt="{{ __('Entrega de caja a compañía de envío') }}" class="w-full object-cover h-32 sm:h-40" loading="lazy">
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </span>
                                <h3 class="text-xl font-bold text-gray-900">{{ __('Almacenamiento') }}</h3>
                            </div>
                            <div class="mt-4 space-y-3 text-sm text-gray-600">
                                <p>{{ __('Tus compras y paquetes pueden permanecer en nuestras instalaciones hasta 30 días sin cargo adicional.') }}</p>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="font-medium text-gray-900">{{ __('Después de 30 días:') }}</p>
                                    <p class="mt-1 text-emerald-600 font-semibold">{{ __('Cargo de almacenamiento: $15 por mes.') }}</p>
                                </div>
                                <p class="text-xs text-gray-500">{{ __('Este cargo aplica tanto a cajas terminadas como a órdenes o paquetes recibidos que permanezcan más de un mes.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto mt-12 max-w-3xl rounded-2xl bg-amber-50 p-6 text-center shadow-inner border border-amber-100" data-reveal>
                <h3 class="flex items-center justify-center gap-2 font-bold text-amber-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    {{ __('Importante') }}
                </h3>
                <p class="mt-3 text-sm text-amber-700">{{ __('Los costos de los productos, impuestos, shipping de las tiendas, envío internacional y cualquier cargo de aduana o importación no están incluidos en nuestros fees de servicio.') }}</p>
                <p class="mt-2 text-sm text-amber-700">{{ __('Cualquier cargo adicional será informado al cliente antes de continuar con el servicio.') }}</p>
                <p class="mt-4 font-semibold text-amber-900">{{ __('Tarifas claras. Compras fáciles. Servicio personalizado.') }}</p>
            </div>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="text-center" data-reveal>
                <span class="section-eyebrow">{{ __('Why choose us') }}</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('The smartest way to shop from the US') }}</h2>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @php $features = [
                    ['0 0 24 24', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z', __('No hidden fees'), __('We quote the full cost — product, fees and shipping — before buying anything. No surprises.'), 'from-emerald-500 to-teal-600'],
                    ['0 0 24 24', 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', __('Smart consolidation'), __('We join all your packages into one box to save you on international shipping.'), 'from-teal-500 to-cyan-600'],
                    ['0 0 24 24', 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z', __('Real-time tracking'), __('Follow every package from Baytown to your door from your private account.'), 'from-emerald-500 to-lime-500'],
                    ['0 0 24 24', 'M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48zM12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z', __('Deals and discounts'), __('We look for the best promotions and coupons so you always pay less.'), 'from-amber-500 to-orange-500'],
                    ['0 0 24 24', 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', __('Secure packing'), __('Your items are packed with care and protection so they arrive in perfect condition.'), 'from-emerald-500 to-green-600'],
                    ['0 0 24 24', 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z', __('Personal attention'), __('Direct communication by WhatsApp at every step of your order.'), 'from-sky-500 to-indigo-500'],
                ]; @endphp

                @foreach ($features as $index => $feat)
                    <div class="card card-hover flex items-start gap-4 p-5" data-reveal style="--reveal-delay: {{ $index * 70 }}ms">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-md shadow-gray-200 {{ $feat[4] }}">
                            <svg class="h-6 w-6" fill="none" viewBox="{{ $feat[0] }}" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat[1] }}" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $feat[2] }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-gray-500">{{ $feat[3] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    <section class="bg-gradient-to-b from-white to-emerald-50/60 py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <div class="grid gap-6 sm:grid-cols-2" data-reveal style="--reveal-delay: 120ms">
                <div class="card p-6">
                    <div class="flex items-start gap-4">
                        <span class="icon-chip shrink-0 bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm-3 3.75c-2.5 0-5 1.5-5 3.75V21h10v-3c0-2.25-2.5-3.75-5-3.75zM6.75 6.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zm10.5 0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ __('Receive your packages in Baytown') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('When you shop online, use this address as your shipping address:') }}</p>
                            <div class="mt-3 flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2.5">
                                <p class="flex-1 text-sm font-medium text-gray-800">{{ \App\Models\Setting::get('warehouse_address') }}</p>
                                <button type="button" data-copy="{{ \App\Models\Setting::get('warehouse_address') }}"
                                        data-title="{{ __('Copy address') }}" data-copied="{{ __('Copied') }}"
                                        class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-emerald-300 hover:text-emerald-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                    </svg>
                                    {{ __('Copy') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-start gap-4">
                        <span class="icon-chip shrink-0 bg-amber-50 text-amber-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ __('Estimated delivery times') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('From Baytown to your door (business days):') }}</p>
                            <div class="mt-3 grid grid-cols-2 gap-x-4 gap-y-1.5">
                                @php $times = [
                                    'MX' => '5 – 7', 'GT' => '7 – 10', 'SV' => '7 – 10', 'HN' => '7 – 10',
                                    'NI' => '7 – 12', 'CR' => '7 – 12', 'PA' => '7 – 12', 'CO' => '7 – 12',
                                    'EC' => '8 – 12', 'PE' => '8 – 12', 'CL' => '10 – 15', 'AR' => '10 – 15',
                                ]; @endphp
                                @foreach ($times as $code => $days)
                                    <div class="flex items-center justify-between rounded-md px-2 py-1 text-sm transition-colors hover:bg-amber-50/60">
                                        <span class="font-medium text-gray-700">{{ country_name($code) }}</span>
                                        <span class="text-xs text-gray-500">{{ $days }} {{ __('days') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="mx-auto max-w-3xl space-y-3" x-data="{ open: null }" data-reveal>
            <div class="text-center">
                <span class="section-eyebrow">{{ __('FAQ') }}</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Frequently asked questions') }}</h2>
            </div>

            @php $faqs = [
                [__('How do I request a purchase?'), __('Just fill out the request form with the product links. We will quote you the total cost including fees and international shipping before buying anything.')],
                [__('How much does the service cost?'), __('We charge a shopper fee per order, a receiving fee per package and a packing fee per box. International shipping is calculated per box based on weight and destination.')],
                [__('How long does a package take to arrive?'), __('Once your box ships from Baytown, delivery to Latin America usually takes between 5 and 15 business days depending on the destination country and carrier.')],
                [__('Can I consolidate several packages into one box?'), __('Yes! That is one of our specialties. We receive your packages in Baytown, consolidate them into one box to save on international shipping and pack everything securely.')],
                [__('How do I pay?'), __('We accept Zelle, bank transfer, card and PayPal. You will see your balance due in your account portal.')],
                [__('Can I track my packages?'), __('Yes. When you create your account you get a private portal where you can follow your purchases, packages and boxes step by step, from Baytown to delivery.')],
                [__('What is your shipping address in the US?'), __('We give you a Baytown, TX address so you can buy online and have your packages delivered there. We receive them, store them and consolidate them for you.')],
                [__('What happens if a package arrives damaged?'), __('We photograph and verify every package when it arrives in Baytown. If something arrives damaged or with issues, we let you know immediately and help you return it or claim the store.')],
            ]; @endphp

            @foreach ($faqs as $index => $faq)
                <div class="card overflow-hidden transition-shadow hover:shadow-md" :class="open === {{ $index }} ? 'border-emerald-200 ring-1 ring-emerald-100' : ''">
                    <button type="button"
                            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-start"
                            @click="open = open === {{ $index }} ? null : {{ $index }}">
                        <span class="font-medium text-gray-900">{{ $faq[0] }}</span>
                        <svg class="h-4.5 w-4.5 shrink-0 text-emerald-500 transition-transform duration-300" :class="open === {{ $index }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open === {{ $index }}" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <p class="border-t border-gray-100 px-5 py-4 text-sm leading-relaxed text-gray-500">{{ $faq[1] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-gray-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="text-center" data-reveal>
                <span class="section-eyebrow">{{ __('What our customers say') }}</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Trusted by shoppers across Latin America') }}</h2>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @php $testimonials = [
                    ['María G.', 'Guatemala', 'Compramos en Nike y Victoria\'s Secret. Todo llegó en una sola caja y en perfecto estado. El portal me muestra todo el proceso.'],
                    ['Carlos P.', 'México', 'Encontraron un descuento que ni yo había visto. El tracking internacional funcionó perfecto hasta mi casa en CDMX.'],
                    ['Sofía R.', 'El Salvador', 'El servicio por WhatsApp es rapidísimo. Me cotizaron todo antes de comprar y no hubo sorpresas con los precios.'],
                ]; @endphp

                @foreach ($testimonials as $index => $t)
                    <figure class="card card-hover p-6" data-reveal style="--reveal-delay: {{ $index * 100 }}ms">
                        <div class="flex gap-1 text-amber-400">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l2.92 6.26 6.58.8-4.9 4.79 1.2 6.65L12 17.3l-5.8 3.2 1.2-6.65-4.9-4.79 6.58-.8L12 2z" />
                                </svg>
                            @endfor
                        </div>
                        <blockquote class="mt-4 text-sm leading-relaxed text-gray-600">“{{ $t[2] }}”</blockquote>
                        <figcaption class="mt-4 flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-bold text-white">
                                {{ substr($t[0], 0, 1) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $t[0] }}</p>
                                <p class="text-xs text-gray-500">{{ $t[1] }}</p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6" data-reveal>
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-600 to-teal-600 px-6 py-14 text-center shadow-xl shadow-emerald-200 sm:px-12">
            <div class="pointer-events-none absolute -start-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -end-16 h-72 w-72 rounded-full bg-teal-300/20 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(255,255,255,0.08)_0%,transparent_60%)]"></div>

            <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Start shopping with us today') }}</h2>
            <p class="relative mx-auto mt-3 max-w-2xl text-emerald-100">
                {{ __('Send us your first request, get your quote and follow your package every step of the way.') }}
            </p>
            <div class="relative mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('request') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-base font-semibold text-emerald-700 shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                    {{ __('Request a Purchase') }}
                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/40 bg-white/10 px-6 py-3 text-base font-semibold text-white backdrop-blur transition-all duration-200 hover:bg-white/20">
                        {{ __('Create Account') }}
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <section id="request" class="mx-auto max-w-7xl px-4 pb-20 sm:px-6">
        <div class="grid gap-12 lg:grid-cols-2">
            <div class="flex flex-col justify-center" data-reveal>
                <span class="section-eyebrow">{{ __('Get started') }}</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Send us your purchase request') }}</h2>
                <p class="mt-3 text-gray-500">{{ __('Tell us what you want. We will quote you the total cost including fees and international shipping before buying anything.') }}</p>

                <div class="mt-10">
                    <h3 class="font-semibold text-gray-900">{{ __('Countries We Ship To') }}</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach (explode(',', \App\Models\Setting::get('countries_served', 'MX,GT,HN,SV,NI,CR,PA,CO,EC,PE,CL,AR')) as $code)
                            <span class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-100">
                                {{ country_name(trim($code)) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div id="contact" class="mt-10">
                    <h3 class="font-semibold text-gray-900">{{ __('Questions about your order?') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Use the chat at the bottom right to send us your questions. We reply right away.') }}</p>
                </div>
            </div>

            <div data-reveal style="--reveal-delay: 150ms">
                <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-xl shadow-emerald-100/60">
                    <div class="flex items-center justify-between bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-4 text-white">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15">
                                <x-brand-logo size="sm" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold">{{ __('Chat with us') }}</p>
                                <p class="flex items-center gap-1.5 text-xs text-emerald-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                    {{ __('Online') }}
                                </p>
                            </div>
                        </div>
                        <svg class="h-5 w-5 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                        </svg>
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-gray-100 px-4 py-3 text-sm leading-relaxed text-gray-700">
                            {{ __('Hi! Tell us the product you want and we will send you a quote.') }}
                        </div>
                        <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-gray-100 px-4 py-3 text-sm leading-relaxed text-gray-700">
                            {{ __('Link, size, color, brand... anything we should know.') }}
                        </div>
                        <a href="{{ route('request') }}" class="btn-primary w-full justify-center">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                            </svg>
                            {{ __('Open chat and send your request') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
