<div>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-900">{{ __('Facturación') }}</h1>
    </x-slot>

    {{-- Financial KPI Cards --}}
    <div class="mb-6 grid gap-4 grid-cols-2 lg:grid-cols-5">
        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">{{ __('Total Facturado') }}</p>
                    <p class="mt-0.5 text-xl font-bold text-gray-900">{{ money($totalInvoiced) }}</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50/90 to-teal-50/50 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">{{ __('Ganancia Servicios') }}</p>
                    <p class="mt-0.5 text-xl font-extrabold text-emerald-800">{{ money($totalEarnings) }}</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-xs">
                    <i class="fa-solid fa-sack-dollar text-sm"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">{{ __('Total Cobrado') }}</p>
                    <p class="mt-0.5 text-xl font-bold text-teal-700">{{ money($totalCollected) }}</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">{{ __('Saldo por Cobrar') }}</p>
                    <p class="mt-0.5 text-xl font-bold {{ $totalPending > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ money($totalPending) }}</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-2xs col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">{{ __('Facturas Emitidas') }}</p>
                    <p class="mt-0.5 text-xl font-bold text-gray-900">{{ $requests->total() }}</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                    <i class="fa-solid fa-receipt text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- INLINE CREATE INVOICE FORM (SIN MODAL, ORGANIZADO POR PREGUNTAS) --}}
    @if ($showCreateForm)
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-white p-6 shadow-md transition-all">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <i class="fa-solid fa-file-invoice text-base"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">
                            {{ $isEditing ? __('Modificar Factura :number', ['number' => $editingRequestNumber]) : __('Emitir Nueva Factura') }}
                        </h2>
                        <p class="text-xs text-gray-500">
                            {{ $isEditing ? __('Actualiza los datos del cliente, productos o tarifas de esta factura.') : __('Selecciona el cliente, ingresa los productos y responde las preguntas de tarifas.') }}
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="closeCreateForm" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form wire:submit="saveInvoice" class="space-y-6">
                {{-- Selector de Modalidad / Tipo de Servicio (3 Opciones) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- 1. Personal Shopper --}}
                    <button type="button" wire:click="setServiceType('shopper')"
                            class="flex items-center gap-3 p-3 rounded-2xl border-2 transition-all text-start {{ $serviceType === 'shopper' ? 'border-emerald-600 bg-emerald-50/80 text-emerald-950 shadow-xs ring-1 ring-emerald-500' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl shrink-0 {{ $serviceType === 'shopper' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ __('Personal Shopper') }}</p>
                            <p class="text-[10.5px] text-gray-500 line-clamp-2 leading-tight mt-0.5">{{ __('Compras físicas + comisión por tramos (20% - 15%)') }}</p>
                        </div>
                    </button>

                    {{-- 2. Comprar Online --}}
                    <button type="button" wire:click="setServiceType('online')"
                            class="flex items-center gap-3 p-3 rounded-2xl border-2 transition-all text-start {{ $serviceType === 'online' ? 'border-blue-600 bg-blue-50/80 text-blue-950 shadow-xs ring-1 ring-blue-500' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl shrink-0 {{ $serviceType === 'online' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                            <i class="fa-solid fa-globe text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ __('Comprar Online') }}</p>
                            <p class="text-[10.5px] text-gray-500 line-clamp-2 leading-tight mt-0.5">{{ __('Comisión 15% + traslado fijo $20 (no se cobra el producto)') }}</p>
                        </div>
                    </button>

                    {{-- 3. Reempaque --}}
                    <button type="button" wire:click="setServiceType('repack')"
                            class="flex items-center gap-3 p-3 rounded-2xl border-2 transition-all text-start {{ $serviceType === 'repack' ? 'border-teal-600 bg-teal-50/80 text-teal-950 shadow-xs ring-1 ring-teal-500' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl shrink-0 {{ $serviceType === 'repack' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                            <i class="fa-solid fa-boxes-packing text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ __('Reempaque') }}</p>
                            <p class="text-[10.5px] text-gray-500 line-clamp-2 leading-tight mt-0.5">{{ __('Cajas Small $15, Med $20, Larga $25 + traslado $20') }}</p>
                        </div>
                    </button>
                </div>

                {{-- Banner Informativo para Compras Online --}}
                @if ($serviceType === 'online')
                    <div class="rounded-xl border border-blue-200 bg-blue-50/90 p-3.5 flex items-start gap-2.5 text-xs text-blue-900 shadow-2xs">
                        <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 text-sm shrink-0"></i>
                        <div>
                            <p class="font-bold">{{ __('Modo Comprar Online Activo') }}:</p>
                            <p class="mt-0.5 leading-relaxed text-blue-800 text-[11.5px]">
                                {{ __('El cliente pagó el valor de los productos en internet. Dicho valor NO se sumará a la factura. La factura cobrará automáticamente la comisión de almacén (15% = ') }}<strong>{{ money($this->productsSubtotal * (($rates['warehouse_percent'] ?? 15) / 100)) }}</strong>{{ __(') y el servicio de traslado fijo de la caja ($20.00).') }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Banner Informativo y Precios Prediseñados para Reempaque --}}
                @if ($serviceType === 'repack')
                    <div class="rounded-2xl border-2 border-teal-300 bg-teal-50/80 p-4 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 text-white shrink-0 shadow-xs">
                                <i class="fa-solid fa-boxes-packing text-base"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-teal-950 flex items-center gap-1.5">
                                        <i class="fa-solid fa-box-open text-teal-700"></i>
                                        {{ __('PRECIOS DE LOS REEMPAQUES') }}
                                    </h4>
                                    <span class="text-[10px] font-bold uppercase bg-teal-200/80 text-teal-900 px-2 py-0.5 rounded-md">
                                        {{ __('Ajuste de Factura') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-teal-900 leading-relaxed">
                                    {{ __('Estos precios son del reempaque si ustedes realizan la compra por cualquier página online y yo recibo aquí en casa. Si sus cajas permanecen un mes o más en nuestro almacén, tendrá un costo adicional de $15 por mes.') }}
                                </p>

                                {{-- Tarjetas de precios prediseñados --}}
                                <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div class="rounded-xl bg-white border border-teal-200 p-2 text-center shadow-2xs">
                                        <p class="text-base font-extrabold text-teal-800">{{ money($rates['box_small_heavy_duty'] ?? 15) }}</p>
                                        <p class="text-[10px] font-bold text-gray-700 uppercase mt-0.5 leading-tight">{{ __('1 Caja Small Heavy Duty') }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-teal-200 p-2 text-center shadow-2xs">
                                        <p class="text-base font-extrabold text-teal-800">{{ money($rates['box_medium_heavy_duty'] ?? 20) }}</p>
                                        <p class="text-[10px] font-bold text-gray-700 uppercase mt-0.5 leading-tight">{{ __('1 Caja Mediana Heavy Duty') }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-teal-200 p-2 text-center shadow-2xs">
                                        <p class="text-base font-extrabold text-teal-800">{{ money($rates['box_large_heavy_duty'] ?? 25) }}</p>
                                        <p class="text-[10px] font-bold text-gray-700 uppercase mt-0.5 leading-tight">{{ __('1 Caja Larga Heavy Duty') }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-teal-200 p-2 text-center shadow-2xs">
                                        <p class="text-base font-extrabold text-teal-800">{{ money($rates['warehouse_delivery_fee'] ?? 20) }}</p>
                                        <p class="text-[10px] font-bold text-gray-700 uppercase mt-0.5 leading-tight">{{ __('Llevar Caja al Almacén') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Selector de Tipo de Factura / Estado (Cotización, Pendiente, Pagada) --}}
                <div class="rounded-xl border border-gray-200/80 bg-gray-50/50 p-3.5">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 mb-2">
                        {{ __('Tipo de Factura / Estado') }} *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        {{-- 1. Cotización --}}
                        <button type="button" wire:click="setInvoiceType('cotizacion')"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border-2 transition-all text-start {{ ($invoiceForm['invoice_type'] ?? $invoiceType) === 'cotizacion' ? 'border-blue-500 bg-blue-50/80 text-blue-950 font-bold shadow-xs ring-1 ring-blue-400' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg shrink-0 {{ ($invoiceForm['invoice_type'] ?? $invoiceType) === 'cotizacion' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                                <i class="fa-solid fa-file-lines text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ __('Cotización') }}</p>
                                <p class="text-[10px] text-gray-500 font-normal leading-tight">{{ __('Presupuesto informativo') }}</p>
                            </div>
                        </button>

                        {{-- 2. Pendiente --}}
                        <button type="button" wire:click="setInvoiceType('pendiente')"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border-2 transition-all text-start {{ ($invoiceForm['invoice_type'] ?? $invoiceType) === 'pendiente' ? 'border-amber-500 bg-amber-50/80 text-amber-950 font-bold shadow-xs ring-1 ring-amber-400' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg shrink-0 {{ ($invoiceForm['invoice_type'] ?? $invoiceType) === 'pendiente' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-500' }}">
                                <i class="fa-solid fa-clock text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ __('Pendiente') }}</p>
                                <p class="text-[10px] text-gray-500 font-normal leading-tight">{{ __('Esperando el pago del cliente') }}</p>
                            </div>
                        </button>

                        {{-- 3. Pagada --}}
                        <button type="button" wire:click="setInvoiceType('pagado')"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border-2 transition-all text-start {{ ($invoiceForm['invoice_type'] ?? $invoiceType) === 'pagado' ? 'border-emerald-500 bg-emerald-50/80 text-emerald-950 font-bold shadow-xs ring-1 ring-emerald-400' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg shrink-0 {{ ($invoiceForm['invoice_type'] ?? $invoiceType) === 'pagado' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ __('Pagado') }}</p>
                                <p class="text-[10px] text-gray-500 font-normal leading-tight">{{ __('Cobro recibido por completo') }}</p>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- 1. Datos del Cliente --}}
                <div class="rounded-xl border border-gray-200/80 bg-gray-50/50 p-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-3 flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">1</span>
                        {{ __('Datos del Cliente') }}
                    </h3>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Seleccionar Cliente') }} *</label>
                            <select wire:model.live="invoiceForm.customer_id"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">{{ __('— Selecciona un cliente —') }}</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email ?: $c->phone ?: 'ID #'.$c->id }})</option>
                                @endforeach
                            </select>
                            @error('invoiceForm.customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Productos y Artículos --}}
                <div class="rounded-xl border border-gray-200/80 bg-gray-50/50 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">2</span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800">
                                @if ($serviceType === 'repack')
                                    {{ __('Paquetes Recibidos para Reempaque') }}
                                @elseif ($serviceType === 'online')
                                    {{ __('Artículos / Compras por Internet') }}
                                @else
                                    {{ __('Productos y Artículos') }}
                                @endif
                            </h3>
                            @if ($serviceType === 'repack')
                                <span class="text-xs font-bold bg-teal-100 text-teal-800 px-2.5 py-0.5 rounded-lg">
                                    {{ __('Valor Declarado: :amount (Pagado en internet por el cliente)', ['amount' => money($this->productsSubtotal)]) }}
                                </span>
                            @elseif ($serviceType === 'online')
                                <span class="text-xs font-bold bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-lg">
                                    {{ __('Valor Online: :amount (Pagado en internet)', ['amount' => money($this->productsSubtotal)]) }}
                                </span>
                            @else
                                <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-lg">
                                    {{ __('Subtotal Productos: :amount', ['amount' => money($this->productsSubtotal)]) }}
                                </span>
                            @endif
                        </div>
                        <button type="button" wire:click="addItem"
                                class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-white px-2.5 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 shadow-2xs">
                            <i class="fa-solid fa-plus text-[10px]"></i> {{ __('Agregar Producto / Paquete') }}
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach ($invoiceForm['items'] as $index => $item)
                            <div class="grid gap-2 rounded-xl border border-gray-200 bg-white p-3 sm:grid-cols-12 items-center shadow-2xs">
                                <div class="sm:col-span-5">
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Producto / Descripción') }} *</label>
                                    <input type="text" wire:model.live.debounce.300ms="invoiceForm.items.{{ $index }}.product_name" placeholder="ej. Zapatos Nike Air Max"
                                           class="mt-1 w-full rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs focus:border-emerald-500">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Tienda / Proveedor') }}</label>
                                    <input type="text" wire:model.live.debounce.300ms="invoiceForm.items.{{ $index }}.store" placeholder="ej. Amazon / Nike"
                                           class="mt-1 w-full rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs focus:border-emerald-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-medium text-gray-500">{{ __('Cant.') }}</label>
                                    <input type="number" min="1" wire:model.live.debounce.300ms="invoiceForm.items.{{ $index }}.quantity"
                                           class="mt-1 w-full rounded-lg border border-gray-300 px-2 py-1.5 text-xs text-center focus:border-emerald-500">
                                </div>
                                <div class="sm:col-span-2 relative">
                                    <label class="block text-[11px] font-medium text-gray-500">
                                        {{ ($serviceType === 'online' || $serviceType === 'repack') ? __('Valor en Internet ($)') : __('Precio ($)') }}
                                    </label>
                                    <div class="flex items-center gap-1 mt-1">
                                        <input type="number" step="0.01" min="0" wire:model.live.debounce.300ms="invoiceForm.items.{{ $index }}.unit_price"
                                               class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-xs focus:border-emerald-500 font-semibold">
                                        @if (count($invoiceForm['items']) > 1)
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-600 p-1">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Cargos y Tarifas Organizados por Preguntas --}}
                <div class="rounded-xl border border-gray-200/80 bg-gray-50/50 p-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">3</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800">{{ __('Cargos y Tarifas (Configuración por Preguntas)') }}</h3>
                    </div>

                    <div class="space-y-4">
                        {{-- Pregunta 1: Personal Shopper (Solo modo Shopper) --}}
                        @if ($serviceType === 'shopper')
                            <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-2xs">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 shrink-0">
                                            <i class="fa-solid fa-cart-shopping text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-900">{{ __('¿Aplica Servicio de Personal Shopper?') }}</h4>
                                            <p class="text-[11px] text-gray-500">
                                                {{ __('Calcula automáticamente según el ajuste de factura:') }}
                                                <span class="font-bold text-emerald-700">
                                                    {{ $this->shopperCommissionCalculation['percent'] }}% de {{ money($this->productsSubtotal) }} = {{ money($this->shopperCommissionCalculation['amount']) }}
                                                </span>
                                                ({{ $this->shopperCommissionCalculation['stores'] }} tiendas / {{ $this->shopperCommissionCalculation['hours'] }} hrs)
                                            </p>
                                        </div>
                                    </div>

                                    <button type="button" wire:click="toggleQuestion('apply_shopper_commission')"
                                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition-all {{ $guidedQuestions['apply_shopper_commission'] ? 'bg-emerald-600 text-white shadow-xs' : 'border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                                        <i class="fa-solid {{ $guidedQuestions['apply_shopper_commission'] ? 'fa-check' : 'fa-xmark' }} text-xs"></i>
                                        <span>{{ $guidedQuestions['apply_shopper_commission'] ? __('Comisión Aplicada') : __('No Aplicar') }}</span>
                                    </button>
                                </div>

                                {{-- Tiendas adicionales --}}
                                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-800">{{ __('¿Se visitaron tiendas adicionales?') }}</p>
                                        <p class="text-[11px] text-gray-500">{{ money($rates['extra_store_fee'] ?? 20) }} por cada tienda adicional</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="decrementQuestion('extra_stores_count')"
                                                class="h-7 w-7 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-gray-100">
                                            <i class="fa-solid fa-minus text-[10px]"></i>
                                        </button>
                                        <span class="w-8 text-center text-xs font-bold text-gray-900">{{ $guidedQuestions['extra_stores_count'] }}</span>
                                        <button type="button" wire:click="incrementQuestion('extra_stores_count')"
                                                class="h-7 w-7 rounded-lg border border-emerald-300 bg-emerald-50 flex items-center justify-center text-emerald-700 hover:bg-emerald-100">
                                            <i class="fa-solid fa-plus text-[10px]"></i>
                                        </button>
                                        <span class="text-xs font-bold text-emerald-700 min-w-[70px] text-end">
                                            {{ money($guidedQuestions['extra_stores_count'] * ($rates['extra_store_fee'] ?? 20)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Pregunta 2: Reempaque en Cajas Heavy Duty --}}
                        <div class="rounded-2xl border transition-all {{ $serviceType === 'repack' ? 'border-teal-300 bg-teal-50/30' : 'border-teal-100 bg-white' }} p-4 shadow-2xs">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-teal-100 text-teal-700 shrink-0">
                                    <i class="fa-solid fa-box text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">{{ __('¿Requiere Reempaque en Cajas Heavy Duty?') }}</h4>
                                    <p class="text-[11px] text-gray-500">{{ __('Selecciona la cantidad de cajas utilizadas según su tamaño:') }}</p>
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                {{-- Caja Small --}}
                                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-gray-800">{{ __('1 Caja Small') }}</span>
                                        <span class="text-xs font-semibold text-teal-700">{{ money($rates['box_small_heavy_duty'] ?? 15) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-gray-500">{{ __('Cantidad:') }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" wire:click="decrementQuestion('boxes_small_count')"
                                                    class="h-6 w-6 rounded-md border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-100">
                                                <i class="fa-solid fa-minus text-[9px]"></i>
                                            </button>
                                            <span class="w-6 text-center text-xs font-bold text-gray-900">{{ $guidedQuestions['boxes_small_count'] }}</span>
                                            <button type="button" wire:click="incrementQuestion('boxes_small_count')"
                                                    class="h-6 w-6 rounded-md border border-teal-300 bg-teal-50 flex items-center justify-center text-teal-700 hover:bg-teal-100">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Caja Mediana --}}
                                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-gray-800">{{ __('1 Caja Mediana') }}</span>
                                        <span class="text-xs font-semibold text-teal-700">{{ money($rates['box_medium_heavy_duty'] ?? 20) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-gray-500">{{ __('Cantidad:') }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" wire:click="decrementQuestion('boxes_medium_count')"
                                                    class="h-6 w-6 rounded-md border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-100">
                                                <i class="fa-solid fa-minus text-[9px]"></i>
                                            </button>
                                            <span class="w-6 text-center text-xs font-bold text-gray-900">{{ $guidedQuestions['boxes_medium_count'] }}</span>
                                            <button type="button" wire:click="incrementQuestion('boxes_medium_count')"
                                                    class="h-6 w-6 rounded-md border border-teal-300 bg-teal-50 flex items-center justify-center text-teal-700 hover:bg-teal-100">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Caja Larga --}}
                                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-gray-800">{{ __('1 Caja Larga') }}</span>
                                        <span class="text-xs font-semibold text-teal-700">{{ money($rates['box_large_heavy_duty'] ?? 25) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-gray-500">{{ __('Cantidad:') }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" wire:click="decrementQuestion('boxes_large_count')"
                                                    class="h-6 w-6 rounded-md border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-100">
                                                <i class="fa-solid fa-minus text-[9px]"></i>
                                            </button>
                                            <span class="w-6 text-center text-xs font-bold text-gray-900">{{ $guidedQuestions['boxes_large_count'] }}</span>
                                            <button type="button" wire:click="incrementQuestion('boxes_large_count')"
                                                    class="h-6 w-6 rounded-md border border-teal-300 bg-teal-50 flex items-center justify-center text-teal-700 hover:bg-teal-100">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pregunta 3: Servicios de Almacén y Logística --}}
                        <div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-2xs">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-blue-700 shrink-0">
                                    <i class="fa-solid fa-warehouse text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">{{ __('Servicios de Almacén y Traslado') }}</h4>
                                    <p class="text-[11px] text-gray-500">{{ __('Recepción en casa/almacén, traslado fijo de cajas y almacenaje prolongado') }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                {{-- Comisión Almacén Compras Online (Solo visible si no es Reempaque) --}}
                                @if ($serviceType !== 'repack')
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between rounded-xl bg-gray-50/50 p-3">
                                        <div>
                                            <p class="text-xs font-bold text-gray-800">
                                                {{ __('Comisión de almacén (15% sobre compras online)') }}
                                            </p>
                                            <p class="text-[11px] text-gray-500">
                                                {{ $rates['warehouse_percent'] ?? 15 }}% de {{ money($this->productsSubtotal) }} =
                                                <strong class="text-blue-700 font-bold">{{ money($this->productsSubtotal * (($rates['warehouse_percent'] ?? 15) / 100)) }}</strong>
                                                @if ($serviceType === 'online')
                                                    <span class="text-xs font-semibold text-blue-600 ms-1">({{ __('Automática en compras online') }})</span>
                                                @endif
                                            </p>
                                        </div>
                                        @if ($serviceType === 'online')
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-blue-600 px-3 py-1 text-xs font-bold text-white">
                                                <i class="fa-solid fa-check text-xs"></i> {{ __('Activada (15%)') }}
                                            </span>
                                        @else
                                            <button type="button" wire:click="toggleQuestion('apply_warehouse_commission')"
                                                    class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1 text-xs font-bold transition-all {{ $guidedQuestions['apply_warehouse_commission'] ? 'bg-blue-600 text-white' : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-100' }}">
                                                <i class="fa-solid {{ $guidedQuestions['apply_warehouse_commission'] ? 'fa-check' : 'fa-xmark' }} text-xs"></i>
                                                <span>{{ $guidedQuestions['apply_warehouse_commission'] ? __('Comisión 15% Aplicada') : __('No Aplicar') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                {{-- Traslado fijo de caja al almacén --}}
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between rounded-xl bg-gray-50/50 p-3">
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">{{ __('Servicio de Traslado de Caja al Almacén') }}</p>
                                        <p class="text-[11px] text-gray-500">
                                            <strong class="text-gray-900">{{ money($rates['warehouse_delivery_fee'] ?? 20) }} {{ __('fijo por caja') }}</strong>
                                            @if ($serviceType === 'online' || $serviceType === 'repack')
                                                <span class="text-xs font-semibold text-blue-600 ms-1">({{ __('Fijo automático $20') }})</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="decrementQuestion('warehouse_delivery_count')"
                                                class="h-6 w-6 rounded-md border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-100">
                                            <i class="fa-solid fa-minus text-[9px]"></i>
                                        </button>
                                        <span class="w-6 text-center text-xs font-bold text-gray-900">{{ $guidedQuestions['warehouse_delivery_count'] }}</span>
                                        <button type="button" wire:click="incrementQuestion('warehouse_delivery_count')"
                                                class="h-6 w-6 rounded-md border border-blue-300 bg-blue-50 flex items-center justify-center text-blue-700 hover:bg-blue-100">
                                            <i class="fa-solid fa-plus text-[9px]"></i>
                                        </button>
                                        <span class="text-xs font-bold text-blue-700 min-w-[70px] text-end">
                                            {{ money($guidedQuestions['warehouse_delivery_count'] * ($rates['warehouse_delivery_fee'] ?? 20)) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Almacenaje por más de 30 días --}}
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between rounded-xl bg-gray-50/50 p-3">
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">{{ __('¿Almacenaje por un mes o más (tras 30 días)?') }}</p>
                                        <p class="text-[11px] text-gray-500">{{ money($rates['monthly_storage_fee'] ?? 15) }}/mes por cada mes adicional</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="decrementQuestion('storage_months_count')"
                                                class="h-6 w-6 rounded-md border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-100">
                                            <i class="fa-solid fa-minus text-[9px]"></i>
                                        </button>
                                        <span class="w-8 text-center text-xs font-bold text-gray-900">{{ $guidedQuestions['storage_months_count'] }} m</span>
                                        <button type="button" wire:click="incrementQuestion('storage_months_count')"
                                                class="h-6 w-6 rounded-md border border-blue-300 bg-blue-50 flex items-center justify-center text-blue-700 hover:bg-blue-100">
                                            <i class="fa-solid fa-plus text-[9px]"></i>
                                        </button>
                                        <span class="text-xs font-bold text-blue-700 min-w-[70px] text-end">
                                            {{ money($guidedQuestions['storage_months_count'] * ($rates['monthly_storage_fee'] ?? 15)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pregunta 4: Otros Cargos o Envío Libre --}}
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs font-semibold text-gray-700">{{ __('¿Deseas agregar otro cargo manual o envío internacional?') }}</span>
                            <button type="button" wire:click="addCustomCost"
                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-plus text-[10px]"></i> {{ __('Agregar Cargo Manual') }}
                            </button>
                        </div>

                        @if (count($customCosts) > 0)
                            <div class="space-y-2">
                                @foreach ($customCosts as $cIdx => $custom)
                                    <div class="grid gap-2 grid-cols-12 items-center bg-white p-2.5 rounded-xl border border-gray-200">
                                        <div class="col-span-7">
                                            <input type="text" wire:model.live.debounce.300ms="customCosts.{{ $cIdx }}.description"
                                                   placeholder="Descripción del cargo adicional"
                                                   class="w-full rounded-lg border border-gray-300 p-1.5 text-xs">
                                        </div>
                                        <div class="col-span-5 flex items-center gap-1">
                                            <input type="number" step="0.01" min="0" wire:model.live.debounce.300ms="customCosts.{{ $cIdx }}.amount"
                                                   placeholder="$ 0.00"
                                                   class="w-full rounded-lg border border-gray-300 p-1.5 text-xs text-end font-bold">
                                            <button type="button" wire:click="removeCustomCost({{ $cIdx }})" class="text-red-400 hover:text-red-600 p-1">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Resumen de Desglose de Cargos Calculados --}}
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3.5">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-900 mb-2">
                                <i class="fa-solid fa-receipt text-emerald-700 mr-1"></i>
                                {{ __('Desglose de Cargos Aplicados a la Factura:') }}
                            </p>
                            <div class="space-y-1.5">
                                @forelse ($invoiceForm['costs'] as $cost)
                                    <div class="flex items-center justify-between text-xs text-emerald-950 font-medium">
                                        <span>• {{ $cost['description'] }}</span>
                                        <span class="font-bold">{{ money($cost['amount']) }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-emerald-800/80 italic">{{ __('No se han seleccionado cargos adicionales.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Datos del Cobro y Pago --}}
                <div class="rounded-xl border border-gray-200/80 bg-emerald-50/40 p-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-3 flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">4</span>
                        {{ __('Datos de Cobro y Pago (Lo que paga el cliente)') }}
                    </h3>

                    @if ($serviceType === 'online' || $serviceType === 'repack')
                        <div class="mb-4 rounded-xl border {{ $serviceType === 'repack' ? 'border-teal-200 text-teal-900' : 'border-blue-200 text-blue-900' }} bg-white p-3 text-xs flex items-center justify-between shadow-2xs">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid {{ $serviceType === 'repack' ? 'fa-boxes-packing text-teal-600' : 'fa-globe text-blue-600' }}"></i>
                                <span>{{ __('Valor Pagado por el Cliente en Internet (No se cobra en esta factura):') }}</span>
                            </div>
                            <span class="font-bold text-gray-500 line-through">{{ money($this->productsSubtotal) }}</span>
                        </div>
                    @endif

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-xl bg-white border border-gray-200 p-3 shadow-2xs">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase">{{ __('Total Facturado') }}</span>
                            <p class="text-xl font-bold text-gray-900 mt-0.5">{{ money($this->invoicedTotal) }}</p>
                            <span class="text-[10px] text-gray-400 font-medium">{{ __('Total a cobrar al cliente') }}</span>
                        </div>

                        <div class="rounded-xl bg-white border border-emerald-200 p-3 shadow-2xs">
                            <span class="text-[11px] font-bold text-emerald-800 uppercase flex items-center gap-1">
                                <i class="fa-solid fa-sack-dollar text-emerald-600 text-xs"></i>
                                {{ __('Ganancia por esta Venta') }}
                            </span>
                            <p class="text-xl font-black text-emerald-700 mt-0.5">{{ money($this->invoicedEarnings) }}</p>
                            <span class="text-[10px] text-emerald-600 font-semibold">{{ __('Ingreso por comisiones y tarifas') }}</span>
                        </div>

                        <div class="rounded-xl bg-white border border-gray-200 p-3 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-gray-500 uppercase">{{ __('Monto Pagado ($)') }}</span>
                                <button type="button" wire:click="payFullAmount" class="text-[11px] text-emerald-700 font-bold hover:underline">
                                    {{ __('Pagar Total') }}
                                </button>
                            </div>
                            <input type="number" step="0.01" min="0" wire:model.live="invoiceForm.amount_paid"
                                   class="mt-1 w-full rounded-lg border border-emerald-400 px-2 py-1 text-sm font-bold text-emerald-700 focus:ring-emerald-500">
                        </div>

                        <div class="rounded-xl bg-white border border-gray-200 p-3 shadow-2xs">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase">{{ __('Saldo Restante') }}</span>
                            <p class="text-xl font-bold mt-0.5 {{ $this->pendingBalance > 0 ? 'text-amber-600' : 'text-emerald-700' }}">
                                {{ money($this->pendingBalance) }}
                            </p>
                            <span class="text-[10px] text-gray-400 font-medium">{{ $this->pendingBalance > 0 ? __('Pendiente de pago') : __('Totalmente pagado') }}</span>
                        </div>
                    </div>

                    {{-- Métodos de Pago disponibles (Visible en Cotización y Pendiente) --}}
                    @if (($invoiceForm['invoice_type'] ?? $invoiceType) === 'cotizacion' || ($invoiceForm['invoice_type'] ?? $invoiceType) === 'pendiente')
                        <div class="mt-4 rounded-2xl border-2 border-indigo-200 bg-gradient-to-br from-indigo-50/90 to-purple-50/70 p-4 shadow-2xs">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="space-y-2 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-600 text-white text-xs">
                                            <i class="fa-solid fa-credit-card text-[10px]"></i>
                                        </span>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-indigo-950">
                                            {{ __('Métodos de Pago Disponibles en la Factura') }}
                                        </h4>
                                        <span class="text-[10px] font-bold bg-indigo-200/80 text-indigo-900 px-2 py-0.5 rounded-md">
                                            {{ __('Visible en Cotización y Pendiente') }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                        <div class="rounded-xl bg-white border border-indigo-100 p-2.5 shadow-2xs flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                <i class="fa-solid fa-z"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[10px] font-bold uppercase text-gray-500">Zelle</p>
                                                <p class="text-xs font-extrabold text-indigo-950 font-mono select-all truncate">Gomez.Lilibeth1977@gmail.com</p>
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-white border border-indigo-100 p-2.5 shadow-2xs flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                <i class="fa-brands fa-paypal"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[10px] font-bold uppercase text-gray-500">PayPal</p>
                                                <p class="text-xs font-extrabold text-indigo-950 font-mono select-all truncate">@speedingshopper</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-indigo-900/80 italic mt-1">
                                        * {{ __('Se adjunta la información gráfica de pago en el PDF oficial.') }}
                                    </p>
                                </div>

                                {{-- Thumbnail of Imagen.png --}}
                                <div class="shrink-0 flex flex-col items-center">
                                    <div class="rounded-xl border border-indigo-200 bg-white p-1.5 shadow-xs">
                                        <img src="{{ asset('images/Imagen.png') }}" alt="Métodos de Pago" class="h-16 w-auto rounded-lg object-contain">
                                    </div>
                                    <span class="text-[9px] text-indigo-700 font-semibold mt-0.5">{{ __('Info Pago') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Dirección de Almacén (Visible cuando la factura está Pagada o Pagado Total) --}}
                    @if (($invoiceForm['invoice_type'] ?? $invoiceType) === 'pagado' || $this->pendingBalance <= 0)
                        <div class="mt-4 rounded-2xl border-2 border-emerald-300 bg-gradient-to-br from-emerald-50 to-teal-50/80 p-4 shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-xs shrink-0">
                                    <i class="fa-solid fa-location-dot text-base"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600 px-2.5 py-0.5 text-[10px] font-extrabold uppercase text-white">
                                            ✓ {{ __('Factura Pagada') }}
                                        </span>
                                        <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">
                                            {{ __('Dirección de Entrega y Almacén') }}
                                        </h4>
                                    </div>
                                    <p class="text-sm font-black text-gray-900 mt-1 font-mono select-all">
                                        7835 Wood Hollow Dr Baytown Tx 77521
                                    </p>
                                    <p class="text-[11px] text-emerald-800 mt-0.5">
                                        {{ __('Esta dirección se adjunta automáticamente en el PDF, la vista de impresión y el correo de confirmación de pago.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Método de Pago') }}</label>
                            <select wire:model="invoiceForm.payment_method" class="w-full rounded-lg border border-gray-300 bg-white p-2 text-xs font-medium">
                                <option value="zelle">Zelle (Gomez.Lilibeth1977@gmail.com)</option>
                                <option value="paypal">PayPal (@speedingshopper)</option>
                                <option value="bank_transfer">{{ __('Transferencia Bancaria') }}</option>
                                <option value="card">{{ __('Tarjeta de Crédito / Débito') }}</option>
                                <option value="cash">{{ __('Efectivo (Cash)') }}</option>
                                <option value="other">{{ __('Otro Método') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Referencia de Pago') }}</label>
                            <input type="text" wire:model="invoiceForm.payment_reference" placeholder="ej. Zelle #48291"
                                   class="w-full rounded-lg border border-gray-300 bg-white p-2 text-xs">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Fecha de Pago') }}</label>
                            <input type="date" wire:model="invoiceForm.paid_at"
                                   class="w-full rounded-lg border border-gray-300 bg-white p-2 text-xs">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Notas de la Factura (opcional)') }}</label>
                        <input type="text" wire:model="invoiceForm.notes" placeholder="Notas visibles o internas"
                               class="w-full rounded-lg border border-gray-300 bg-white p-2 text-xs">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" wire:click="closeCreateForm"
                            class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm transition-all">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>{{ $isEditing ? __('Actualizar Factura') : __('Guardar y Emitir Factura') }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Invoices Table Card --}}
    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-3 text-xs text-gray-400"></i>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Buscar por factura, cliente, teléfono...') }}"
                       class="w-full rounded-xl border border-gray-200 bg-gray-50/50 py-2 ps-9 pe-4 text-xs focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
            </div>

            <div class="flex items-center gap-2">
                @if (! $showCreateForm)
                    <button type="button" wire:click="openCreateForm"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition-all">
                        <i class="fa-solid fa-plus text-base"></i>
                        <span>{{ __('Nueva Factura') }}</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('N° Factura') }}</th>
                        <th class="px-4 py-3">{{ __('Cliente') }}</th>
                        <th class="px-4 py-3">{{ __('Productos / Resumen') }}</th>
                        <th class="px-4 py-3">{{ __('Presupuesto / Producto') }}</th>
                        <th class="px-4 py-3">{{ __('Total Facturado') }}</th>
                        <th class="px-4 py-3">{{ __('Ganancia (Comisión)') }}</th>
                        <th class="px-4 py-3">{{ __('Monto Pagado') }}</th>
                        <th class="px-4 py-3">{{ __('Saldo') }}</th>
                        <th class="px-4 py-3">{{ __('Estado') }}</th>
                        <th class="px-4 py-3">{{ __('Fecha') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse ($requests as $request)
                        @php
                            $totalCost = (float) $request->total_cost;
                            $productCost = (float) $request->costItems->where('type', \App\Enums\CostType::ProductCost)->sum('amount');
                            if ($productCost == 0.0 && $request->unit_price) {
                                $productCost = (float) $request->unit_price * max(1, $request->quantity);
                            }
                            $earnings = (float) $request->costItems->where('type', '!=', \App\Enums\CostType::ProductCost)->sum('amount');
                            $payments = $paymentsByRequest->get($request->id) ?? collect();
                            $paidAmount = (float) $payments->sum('amount_paid');
                            $balance = max(0.0, $totalCost - $paidAmount);
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-gray-900">
                                <a href="{{ route('admin.requests.show', $request) }}" wire:navigate class="hover:text-emerald-700 hover:underline">
                                    {{ $request->number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @if ($request->customer)
                                    <a href="{{ route('admin.customers.show', $request->customer) }}" wire:navigate class="hover:text-emerald-700">
                                        {{ $request->customer->name }}
                                    </a>
                                    @if ($request->customer->whatsapp)
                                        <p class="text-[11px] text-gray-400 font-normal">{{ $request->customer->whatsapp }}</p>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 max-w-xs truncate">
                                <span class="font-medium">{{ $request->product_name }}</span>
                                @if ($request->quantity > 1)
                                    <span class="text-gray-400 font-normal">({{ $request->quantity }} uds)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-600">
                                {{ $productCost > 0 ? money($productCost) : '—' }}
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-900">
                                {{ money($totalCost) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 shadow-2xs" title="{{ __('Ganancia neta estimada de la empresa') }}">
                                    💰 {{ money($earnings) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-teal-700">
                                {{ money($paidAmount) }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($balance <= 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700">
                                        <i class="fa-solid fa-check text-[10px]"></i> {{ __('Pagada') }}
                                    </span>
                                @else
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-700">
                                            {{ money($balance) }}
                                        </span>
                                        @if ($paidAmount > 0)
                                            <span class="text-[10px] text-amber-600 font-medium mt-0.5">{{ __('Pendiente') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$request->status" />
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $request->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3 text-end space-x-1">
                                <button type="button" wire:click="editInvoice({{ $request->id }})" title="{{ __('Editar Factura') }}"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>

                                <a href="{{ route('admin.requests.print', $request) }}" target="_blank" title="{{ __('Imprimir Factura con QR') }}"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>

                                @if ($balance > 0)
                                    <button type="button" wire:click="openPaymentModal({{ $request->id }})" title="{{ __('Registrar Abono / Pago') }}"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100">
                                        <i class="fa-solid fa-credit-card text-xs"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-file-invoice text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm">{{ __('No se encontraron facturas registradas.') }}</p>
                                <button type="button" wire:click="openCreateForm" class="btn-primary mt-3 text-xs">
                                    <i class="fa-solid fa-plus text-xs"></i> {{ __('Crear la primera factura') }}
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="border-t border-gray-100 p-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    {{-- Record Payment / Abono Modal --}}
    @if ($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closePaymentModal"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
                 x-on:keydown.escape.window="$wire.closePaymentModal()">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2 text-gray-900">
                        <i class="fa-solid fa-credit-card text-emerald-600"></i>
                        <h3 class="text-base font-bold">{{ __('Registrar Abono a Factura') }}</h3>
                    </div>
                    <button type="button" wire:click="closePaymentModal" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form wire:submit="savePayment" class="mt-4 space-y-4">
                    <div class="rounded-xl bg-gray-50 p-3 text-xs space-y-1">
                        <p><strong>{{ __('Factura:') }}</strong> {{ $paymentForm['request_number'] ?? '' }}</p>
                        <p><strong>{{ __('Cliente:') }}</strong> {{ $paymentForm['customer_name'] ?? '' }}</p>
                        <p><strong>{{ __('Total Factura:') }}</strong> {{ money($paymentForm['invoice_total'] ?? 0) }}</p>
                        <p><strong>{{ __('Ya Pagado:') }}</strong> {{ money($paymentForm['already_paid'] ?? 0) }}</p>
                        <p class="text-amber-700 font-bold"><strong>{{ __('Saldo Pendiente:') }}</strong> {{ money($paymentForm['pending_balance'] ?? 0) }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700">{{ __('Monto a Abonar ($)') }} *</label>
                        <input type="number" step="0.01" min="0.01" wire:model="paymentForm.amount_paid"
                               class="w-full rounded-xl border border-gray-300 p-2.5 text-sm font-bold text-emerald-700 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700">{{ __('Método de Pago') }} *</label>
                        <select wire:model="paymentForm.payment_method" class="w-full rounded-xl border border-gray-300 p-2 text-xs">
                            <option value="zelle">Zelle</option>
                            <option value="cash">{{ __('Efectivo (Cash)') }}</option>
                            <option value="card">{{ __('Tarjeta de Crédito/Débito') }}</option>
                            <option value="transfer">{{ __('Transferencia Bancaria') }}</option>
                            <option value="paypal">PayPal</option>
                            <option value="other">{{ __('Otro') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700">{{ __('Referencia de Pago') }}</label>
                        <input type="text" wire:model="paymentForm.reference" placeholder="ej. Zelle confirmación #3921"
                               class="w-full rounded-xl border border-gray-300 p-2 text-xs">
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-4">
                        <button type="button" wire:click="closePaymentModal" class="rounded-xl border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            {{ __('Registrar Pago') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
