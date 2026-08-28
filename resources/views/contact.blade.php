<x-public-layout title="{{ __('Contacto') }} | {{ \App\Models\Setting::get('company_name', config('app.name')) }}">
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-50/70 via-white to-white py-16 lg:py-24">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -start-32 top-0 h-96 w-96 rounded-full bg-emerald-100/80 blur-3xl"></div>
            <div class="absolute -end-24 bottom-0 h-96 w-96 rounded-full bg-teal-100/80 blur-3xl"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(5,150,105,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(5,150,105,0.04)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_70%_50%_at_50%_0%,black,transparent)]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
            <div class="text-center" data-reveal>
                <span class="section-eyebrow">{{ __('Atención personalizada') }}</span>
                <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                    {{ __('Contáctanos') }}
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600">
                    {{ __('Estamos en Baytown, Texas. Escríbenos o llena el formulario y te asesoramos de inmediato con tus compras y envíos a Latinoamérica.') }}
                </p>
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-12 lg:gap-12">
                <!-- Left Column: Contact Cards & Info -->
                <div class="space-y-6 lg:col-span-5" data-reveal>
                    <!-- WhatsApp Card -->
                    @php
                        $dbPhone = \App\Models\Setting::get('whatsapp_phone');
                        $cleanPhone = $dbPhone ? preg_replace('/\D+/', '', $dbPhone) : '';
                        $rawPhone = !empty($cleanPhone) ? $cleanPhone : '13462333199';
                        $displayPhone = !empty($dbPhone) ? $dbPhone : '+1 (346) 233-3199';
                    @endphp
                    <div class="card overflow-hidden border-emerald-200/80 bg-gradient-to-br from-emerald-50/80 via-white to-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-200">
                                <i class="fa-brands fa-whatsapp text-2xl"></i>
                            </span>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-bold text-gray-900">{{ __('WhatsApp Directo') }}</h3>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        {{ __('En línea') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Chatea directamente con nosotros para cotizaciones rápidas, fotos y asesoría en compras.') }}</p>
                                <div class="mt-4">
                                    @if ($rawPhone)
                                        <a href="https://wa.me/{{ $rawPhone }}?text={{ urlencode('¡Hola! Me gustaría información sobre sus servicios de compras y envíos.') }}"
                                           target="_blank"
                                           class="btn-primary inline-flex !w-auto items-center gap-2 !bg-emerald-600 !py-2 text-sm hover:!bg-emerald-700">
                                            <i class="fa-brands fa-whatsapp text-base"></i>
                                            {{ __('Escribir por WhatsApp') }}
                                        </a>
                                    @else
                                        <span class="text-sm font-semibold text-gray-800">{{ $displayPhone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email & Warehouse Info -->
                    <div class="card space-y-5 p-6">
                        <div class="flex items-start gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-100 text-teal-600">
                                <i class="fa-solid fa-envelope text-xl"></i>
                            </span>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ __('Correo Electrónico') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('Para cotizaciones formales y seguimiento') }}</p>
                                <a href="mailto:{{ \App\Models\Setting::get('mail_from_address', 'contacto@speedshopper.com') }}"
                                   class="mt-1 inline-block text-sm font-medium text-emerald-600 transition-colors hover:text-emerald-700">
                                    {{ \App\Models\Setting::get('mail_from_address', 'contacto@speedshopper.com') }}
                                </a>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <div class="flex items-start gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                                <i class="fa-solid fa-location-dot text-xl"></i>
                            </span>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ __('Bodega y Recepción en USA') }}</h3>
                                <p class="mt-1 text-sm font-medium text-gray-800">{{ \App\Models\Setting::get('warehouse_address', 'Baytown, TX 77521, USA') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Países a los que enviamos -->
                    <div class="card p-6">
                        <h3 class="flex items-center gap-2 font-bold text-gray-900 text-sm">
                            <i class="fa-solid fa-plane-departure text-emerald-600"></i>
                            {{ __('Países a los que enviamos') }}
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Realizamos envíos puerta a puerta a los siguientes países:') }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach (countries_served_list() as $code)
                                <span class="rounded-lg border border-emerald-100 bg-emerald-50/80 px-2.5 py-1 text-xs font-semibold text-emerald-800 shadow-sm">
                                    <i class="fa-solid fa-circle-check text-[10px] text-emerald-500 me-1"></i>
                                    {{ country_name($code) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Contact Form -->
                <div class="lg:col-span-7" data-reveal style="--reveal-delay: 100ms">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Envíanos un mensaje') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Completa el formulario y te responderemos con toda la información necesaria.') }}</p>
                    </div>

                    <livewire:contact-form />
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-20 border-t border-gray-200/80 pt-16" data-reveal>
                <div class="text-center">
                    <span class="section-eyebrow">{{ __('Preguntas Frecuentes') }}</span>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl">{{ __('¿Tienes dudas sobre el proceso?') }}</h2>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <div class="card p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fa-solid fa-cart-shopping text-lg"></i>
                        </div>
                        <h3 class="mt-4 font-bold text-gray-900">{{ __('¿Cómo hago un pedido?') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500">
                            {{ __('Puedes llenar nuestro formulario de solicitud o escribirnos directamente con los links o fotos de los productos que deseas.') }}
                        </p>
                    </div>

                    <div class="card p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-600">
                            <i class="fa-solid fa-tags text-lg"></i>
                        </div>
                        <h3 class="mt-4 font-bold text-gray-900">{{ __('¿Cuáles son las tarifas?') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500">
                            {{ __('Nuestras tarifas van desde el 15% al 20% según el presupuesto de tu compra, con tiendas y horas incluidas.') }}
                        </p>
                        <a href="{{ route('home') }}#fees" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            {{ __('Ver tabla de fees') }} <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <div class="card p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                            <i class="fa-solid fa-truck-fast text-lg"></i>
                        </div>
                        <h3 class="mt-4 font-bold text-gray-900">{{ __('¿Cómo sigo mi envío?') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500">
                            {{ __('Desde tu portal privado podrás ver cada paquete recibido, peso, estado de consolidación y número de rastreo internacional.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
