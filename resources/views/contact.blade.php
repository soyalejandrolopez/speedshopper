<x-public-layout title="{{ __('Contacto') }} | {{ \App\Models\Setting::get('company_name', config('app.name')) }}">
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-50/70 via-white to-white py-8 sm:py-14 lg:py-20">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -start-32 top-0 h-96 w-96 rounded-full bg-emerald-100/80 blur-3xl"></div>
            <div class="absolute -end-24 bottom-0 h-96 w-96 rounded-full bg-teal-100/80 blur-3xl"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(5,150,105,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(5,150,105,0.04)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_70%_50%_at_50%_0%,black,transparent)]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-3.5 sm:px-6">
            <div class="text-center" data-reveal>
                <span class="section-eyebrow">{{ __('Atención personalizada') }}</span>
                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                    {{ __('Contáctanos') }}
                </h1>
                <p class="mx-auto mt-3 max-w-2xl text-sm sm:text-base text-gray-600 leading-relaxed">
                    {{ __('Estamos en Baytown, Texas. Escríbenos o llena el formulario y te asesoramos de inmediato con tus compras y envíos a Latinoamérica.') }}
                </p>
            </div>

            <div class="mt-8 sm:mt-12 grid gap-8 lg:grid-cols-12 lg:gap-12">
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
                                @php
                                    $contactEmail = \App\Models\Setting::get('contact_email', 'Speedingshopper@gmail.com');
                                    if (empty($contactEmail) || str_contains($contactEmail, 'speedshopper.com')) {
                                        $contactEmail = 'Speedingshopper@gmail.com';
                                    }
                                @endphp
                                <a href="mailto:{{ $contactEmail }}"
                                   class="mt-1 inline-block text-sm font-semibold text-emerald-600 transition-colors hover:text-emerald-700">
                                    {{ $contactEmail }}
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

                    <!-- Redes Sociales / Instagram Card -->
                    @php
                        $instagramUrl = \App\Models\Setting::get('instagram_url', 'https://www.instagram.com/speedingshopper');
                    @endphp
                    <div class="card overflow-hidden border-pink-100 bg-gradient-to-br from-pink-50/50 via-purple-50/30 to-white p-6 shadow-md transition-all hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#f09433] via-[#dc2743] to-[#bc1888] text-white shadow-md shadow-pink-500/20">
                                <i class="fa-brands fa-instagram text-2xl"></i>
                            </span>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">{{ __('Síguenos en Instagram') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Descubre compras recientes, ofertas en vivo, tips de tiendas en USA y novedades.') }}</p>
                                <div class="mt-4">
                                    <a href="{{ $instagramUrl }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#833AB4] via-[#FD1D1D] to-[#F77737] px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-pink-500/20 transition-all duration-200 hover:scale-[1.02] hover:shadow-lg hover:shadow-pink-500/30">
                                        <i class="fa-brands fa-instagram text-base"></i>
                                        <span>@speedingshopper</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-80 ms-1"></i>
                                    </a>
                                </div>
                            </div>
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
