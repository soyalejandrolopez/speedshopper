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
                 class="h-full w-full object-cover object-bottom" fetchpriority="high">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/35 to-black/15"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/25"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-4xl flex-col justify-center px-4 pt-64 pb-8 text-center sm:px-6">
            <div class="flex flex-col items-center" data-reveal>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-black/20 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                    </span>
                    {{ __('Personal Shopper in Baytown, TX') }}
                </span>

                <h1 class="mt-6 text-3xl font-extrabold leading-[1.08] tracking-tight text-white sm:text-4xl lg:text-5xl">
                    {{ __('We buy, receive and ship') }}
                    <span class="text-white">{{ __('your products to Latin America') }}</span>
                </h1>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('request') }}" class="btn-primary px-6 py-3 text-base">
                        {{ __('New Order') }}
                        <i class="fa-solid fa-arrow-right text-lg"></i>
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/40 bg-white/10 px-6 py-3 text-base font-semibold text-white backdrop-blur transition-all duration-200 hover:bg-white/20">
                            {{ __('Create Account') }}
                        </a>
                    @endguest
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[10px] font-medium text-gray-300" data-reveal>
                    <span class="inline-flex items-center gap-1.5">
<i class="fa-solid fa-circle-check text-xs text-white"></i>
                        Zelle
                    </span>
                    <span class="inline-flex items-center gap-1.5">
<i class="fa-solid fa-circle-check text-xs text-white"></i>
                        PayPal
                    </span>


                </div>


            </div>
        </div>
    </section>

    <!-- QUICK QUOTE SECTION AT TOP -->
    <section id="quick-quote" class="relative -mt-12 z-20 mx-auto max-w-7xl px-4 pb-12 sm:px-6">
        <div class="rounded-3xl border border-emerald-100/80 bg-white p-6 sm:p-8 lg:p-10 shadow-2xl shadow-emerald-950/10">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                
                <!-- Left Details & Value Props (2nd on mobile, 1st on desktop) -->
                <div class="order-2 lg:order-1 lg:col-span-5 flex flex-col justify-between" data-reveal>
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 uppercase tracking-wide">
                            <i class="fa-solid fa-bolt text-xs text-emerald-600"></i>
                            Quick Quote
                        </span>

                        <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                            {{ __('Envíanos tu solicitud de compra') }}
                        </h2>

                        <p class="mt-3 text-base text-gray-600 leading-relaxed">
                            {{ __('Cuéntanos qué quieres y te cotizamos el costo total antes de comprar cualquier cosa.') }}
                        </p>

                        <!-- Benefit bullets -->
                        <div class="mt-6 space-y-3.5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ __('Cotización gratis, sin compromiso') }}</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ __('Envía el link del producto y nosotros nos encargamos del resto.') }}</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ __('Buscamos las mejores ofertas y descuentos para ti.') }}</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mt-0.5">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                                <span class="text-sm font-semibold text-emerald-700">{{ __('Respuesta rápida por chat · Sin pago por adelantado') }}</span>
                            </div>
                        </div>

                        <!-- Consolidación de Paquetes Highlight Card -->
                        <div class="mt-6 rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-teal-50/60 to-white p-4 shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-600/20">
                                    <i class="fa-solid fa-boxes-packing text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ __('Consolidación de Paquetes') }}</h4>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ __('Consolidamos todos tus paquetes en una sola caja.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Stats Metrics Row -->
                    <div class="mt-8 pt-6 border-t border-gray-100 grid grid-cols-3 gap-2.5 sm:gap-3 text-center">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3 transition-colors hover:bg-emerald-50/60 hover:border-emerald-200">
                            <p class="text-2xl font-black text-emerald-600 tracking-tight">12+</p>
                            <p class="text-[11px] font-semibold text-gray-700 mt-1 leading-tight">{{ __('Países a los que enviamos') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3 transition-colors hover:bg-emerald-50/60 hover:border-emerald-200">
                            <p class="text-2xl font-black text-emerald-600 tracking-tight">&lt; 1h</p>
                            <p class="text-[11px] font-semibold text-gray-700 mt-1 leading-tight">{{ __('Respondemos en menos de 1 hora por chat.') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3 transition-colors hover:bg-emerald-50/60 hover:border-emerald-200">
                            <p class="text-2xl font-black text-emerald-600 tracking-tight">100%</p>
                            <p class="text-[11px] font-semibold text-gray-700 mt-1 leading-tight">{{ __('Sin pago por adelantado') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Form: Public Request Form / Quick Quote (1st on mobile, 2nd on desktop) -->
                <div class="order-1 lg:order-2 lg:col-span-7" data-reveal style="--reveal-delay: 100ms">
                    <div class="overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-xl shadow-gray-200/50">
                        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 px-6 py-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-bold">{{ __('Cotizador Rápido') }}</h3>
                                    <p class="text-xs text-emerald-100">{{ __('Completa tus datos y los links o nombres de lo que buscas') }}</p>
                                </div>
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                                    <i class="fa-solid fa-bolt text-white text-sm"></i>
                                </span>
                            </div>
                        </div>
                        <livewire:public-request-form />
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="how-it-works" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="text-center" data-reveal>
            <span class="section-eyebrow">{{ __('How it works') }}</span>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Fácil, rápido y confiable. Nosotros nos encargamos del proceso por ti.') }}</h2>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-3">
            @php
                $steps = [
                    ['title' => __('Envía tu pedido'), 'text' => __('Compártenos el link, foto o nombre del producto que deseas. También podemos ayudarte a encontrar ofertas y mejores precios.'), 'icon' => 'fa-solid fa-link', 'color' => 'bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white'],
                    ['title' => __('Compramos o recibimos tu paquete'), 'text' => __('Hacemos la compra por ti, o recibimos en Baytown, Texas, los paquetes que compres online en tus tiendas favoritas.'), 'icon' => 'fa-solid fa-box', 'color' => 'bg-teal-100 text-teal-600 group-hover:bg-teal-600 group-hover:text-white'],
                    ['title' => __('Empacamos y enviamos'), 'text' => __('Organizamos, empacamos y enviamos tus productos a Latinoamérica de forma segura, con seguimiento de tu envío.'), 'icon' => 'fa-solid fa-truck-fast', 'color' => 'bg-sky-100 text-sky-600 group-hover:bg-sky-600 group-hover:text-white'],
                ];
            @endphp

            @foreach ($steps as $index => $step)
                <div class="card card-hover group relative p-6 pt-10 text-center" data-reveal style="--reveal-delay: {{ $index * 100 }}ms">
                    <span class="absolute top-4 end-4 flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-400">{{ $index + 1 }}</span>
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl transition-all duration-300 {{ $step['color'] }} group-hover:scale-110 group-hover:rotate-3">
                        <i class="{{ $step['icon'] }} text-3xl"></i>
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
                <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">{{ __('Costos y Fees') }}</h2>
                <p class="mt-4 text-xl font-medium text-emerald-600 sm:text-2xl">{{ __('Tarifas claras. Compras fáciles. Servicio personalizado.') }}</p>
            </div>
            
            <div class="mt-16 grid gap-8 lg:grid-cols-2">
                <div class="card p-8" data-reveal>
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                            <i class="fa-solid fa-bag-shopping text-xl"></i>
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
                                <i class="fa-solid fa-globe text-xl"></i>
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
                                <i class="fa-solid fa-box text-xl"></i>
                            </span>
                            <h3 class="text-xl font-bold text-gray-900">{{ __('Servicio de Embalaje') }}</h3>
                        </div>
                        <div class="mt-6">
                            <p class="mb-4 text-sm text-gray-500">{{ __('El costo del embalaje dependerá del tamaño de la caja utilizada:') }}</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="rounded-lg bg-gray-50 p-3 text-center transition-colors hover:bg-sky-50">
                                    <div class="text-xs font-semibold uppercase text-gray-500">{{ __('Small') }}</div>
                                    <div class="mt-1 text-lg font-bold text-emerald-600">$15</div>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3 text-center transition-colors hover:bg-sky-50">
                                    <div class="text-xs font-semibold uppercase text-gray-500">{{ __('Mediana') }}</div>
                                    <div class="mt-1 text-lg font-bold text-emerald-600">$20</div>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3 text-center transition-colors hover:bg-sky-50">
                                    <div class="text-xs font-semibold uppercase text-gray-500">{{ __('Large') }}</div>
                                    <div class="mt-1 text-lg font-bold text-emerald-600">$25</div>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-500">{{ __('El servicio incluye la preparación y organización de tus productos para su envío.') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="card p-8" data-reveal>
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                            <i class="fa-solid fa-truck-fast text-xl"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('Entrega a la compañía de envío') }}</h3>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">{{ __('Enviamos tu caja preparada a la compañía de envío seleccionada.') }}</p>
                    <div class="mt-4 rounded-lg bg-gray-50 p-3">
                        <p class="text-emerald-600 font-semibold">{{ __('Precio: $20') }}</p>
                    </div>
                </div>
                
                <div class="card p-8" data-reveal style="--reveal-delay: 100ms">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                            <i class="fa-solid fa-clock text-xl"></i>
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

            <div class="mx-auto mt-12 max-w-3xl rounded-2xl bg-amber-50 p-8 sm:p-10 text-center shadow-inner border border-amber-100" data-reveal>
                <h3 class="flex items-center justify-center gap-2 text-xl font-bold text-amber-800">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    {{ __('Importante') }}
                </h3>
                <p class="mt-4 text-base text-amber-700">{{ __('Los costos de los productos, impuestos, shipping de las tiendas, envío internacional y cualquier cargo de aduana o importación no están incluidos en nuestros fees de servicio.') }}</p>
                <p class="mt-2 text-base text-amber-700">{{ __('Cualquier cargo adicional será informado al cliente antes de continuar con el servicio.') }}</p>
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
                    ['fa-solid fa-credit-card', __('No hidden fees'), __('We quote the full cost — product, fees and shipping — before buying anything. No surprises.'), 'from-emerald-500 to-teal-600'],
                    ['fa-solid fa-boxes-stacked', __('Smart consolidation'), __('We join all your packages into one box to save you on international shipping.'), 'from-teal-500 to-cyan-600'],
                    ['fa-solid fa-table-cells', __('Real-time tracking'), __('Follow every package from Baytown to your door from your private account.'), 'from-emerald-500 to-lime-500'],
                    ['fa-solid fa-fire', __('Deals and discounts'), __('We look for the best promotions and coupons so you always pay less.'), 'from-amber-500 to-orange-500'],
                    ['fa-solid fa-shield-halved', __('Secure packing'), __('Your items are packed with care and protection so they arrive in perfect condition.'), 'from-emerald-500 to-green-600'],
                    ['fa-brands fa-whatsapp', __('Personal attention'), __('Direct communication by WhatsApp at every step of your order.'), 'from-sky-500 to-indigo-500'],
                ]; @endphp

                @foreach ($features as $index => $feat)
                    <div class="card card-hover flex items-start gap-4 p-5" data-reveal style="--reveal-delay: {{ $index * 70 }}ms">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-md shadow-gray-200 {{ $feat[3] }}">
                            <i class="{{ $feat[0] }} text-2xl"></i>
                        </span>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $feat[1] }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-gray-500">{{ $feat[2] }}</p>
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
                            <i class="fa-solid fa-location-dot text-xl"></i>
                        </span>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ __('Receive your packages in Baytown') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('When you shop online, use this address as your shipping address:') }}</p>
                            <div class="mt-3 flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2.5">
                                <p class="flex-1 text-sm font-medium text-gray-800">{{ \App\Models\Setting::get('warehouse_address') }}</p>
                                <button type="button" data-copy="{{ \App\Models\Setting::get('warehouse_address') }}"
                                        data-title="{{ __('Copy address') }}" data-copied="{{ __('Copied') }}"
                                        class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-emerald-300 hover:text-emerald-600">
                                    <i class="fa-solid fa-copy text-sm"></i>
                                    {{ __('Copy') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-start gap-4">
                        <span class="icon-chip shrink-0 bg-amber-50 text-amber-600">
                            <i class="fa-solid fa-clock text-xl"></i>
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
                        <i class="fa-solid fa-chevron-down text-lg text-emerald-500 transition-transform duration-300" :class="open === {{ $index }} ? 'rotate-180' : ''"></i>
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
                                <i class="fa-solid fa-star text-lg"></i>
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
                    <i class="fa-solid fa-arrow-right text-lg"></i>
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
                        @foreach (explode(',', \App\Models\Setting::get('countries_served', 'VE,CO,EC,PE,CL,CR,PA,DO,SV,HN,MX')) as $code)
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
                        <i class="fa-solid fa-comments text-xl text-emerald-100"></i>
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-gray-100 px-4 py-3 text-sm leading-relaxed text-gray-700">
                            {{ __('Hi! Tell us the product you want and we will send you a quote.') }}
                        </div>
                        <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-gray-100 px-4 py-3 text-sm leading-relaxed text-gray-700">
                            {{ __('Link, size, color, brand... anything we should know.') }}
                        </div>
                        <a href="{{ route('request') }}" class="btn-primary w-full justify-center">
                            <i class="fa-solid fa-comments text-base"></i>
                            {{ __('Open chat and send your request') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
