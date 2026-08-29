<div>
    <x-slot name="header">{{ __('Facturación') }}</x-slot>

    {{-- Financial KPI Cards --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Total Facturado') }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ money($totalInvoiced) }}</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Total Cobrado') }}</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-700">{{ money($totalCollected) }}</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Saldo por Cobrar') }}</p>
                    <p class="mt-1 text-2xl font-bold {{ $totalPending > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ money($totalPending) }}</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="button" wire:click="openCreateModal"
                    class="w-full flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 p-5 text-base font-bold text-white shadow-lg shadow-emerald-200 transition-all hover:bg-emerald-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                <i class="fa-solid fa-plus text-lg"></i>
                <span>{{ __('Crear Nueva Factura') }}</span>
            </button>
        </div>
    </div>

    {{-- Invoices Table Card --}}
    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-3 text-xs text-gray-400"></i>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Buscar por factura, cliente, teléfono...') }}"
                       class="w-full rounded-xl border border-gray-200 bg-gray-50/50 py-2 ps-9 pe-4 text-xs focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.rates.index') }}" wire:navigate
                   class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">
                    <i class="fa-solid fa-tags text-emerald-600"></i>
                    <span>{{ __('Configurar Tarifario PDF') }}</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('N° Factura') }}</th>
                        <th class="px-4 py-3">{{ __('Cliente') }}</th>
                        <th class="px-4 py-3">{{ __('Productos / Resumen') }}</th>
                        <th class="px-4 py-3">{{ __('Total Facturado') }}</th>
                        <th class="px-4 py-3">{{ __('Monto Pagado') }}</th>
                        <th class="px-4 py-3">{{ __('Saldo') }}</th>
                        <th class="px-4 py-3">{{ __('Fecha') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse ($requests as $request)
                        @php
                            $totalCost = (float) $request->total_cost;
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
                            <td class="px-4 py-3 font-semibold text-gray-900">
                                {{ money($totalCost) }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-emerald-600">
                                {{ money($paidAmount) }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($balance <= 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700">
                                        <i class="fa-solid fa-check text-[10px]"></i> Pagada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-700">
                                        {{ money($balance) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $request->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3 text-end space-x-1">
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
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-file-invoice text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm">{{ __('No se encontraron facturas registradas.') }}</p>
                                <button type="button" wire:click="openCreateModal" class="btn-primary mt-3 text-xs">
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

    {{-- Create Invoice Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="closeCreateModal"></div>

            <div class="relative w-full max-w-3xl my-8 rounded-2xl bg-white shadow-2xl overflow-hidden"
                 x-on:keydown.escape.window="$wire.closeCreateModal()">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ __('Nueva Factura') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Ingresa los datos del cliente, los productos y el pago inicial.') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeCreateModal" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form wire:submit="saveInvoice" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    {{-- 1. Datos del Cliente --}}
                    <div class="rounded-xl border border-gray-200/80 bg-gray-50/40 p-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-3 flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">1</span>
                            {{ __('Datos del Cliente') }}
                        </h4>

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

                    {{-- 2. Productos de la Factura --}}
                    <div class="rounded-xl border border-gray-200/80 bg-gray-50/40 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-2">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">2</span>
                                {{ __('Productos y Artículos') }}
                            </h4>
                            <button type="button" wire:click="addItem"
                                    class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                                <i class="fa-solid fa-plus text-[10px]"></i> {{ __('Agregar Producto') }}
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach ($invoiceForm['items'] as $index => $item)
                                <div class="grid gap-2 rounded-xl border border-gray-200 bg-white p-3 sm:grid-cols-12 items-center">
                                    <div class="sm:col-span-5">
                                        <label class="block text-[11px] font-medium text-gray-500">{{ __('Producto / Descripción') }} *</label>
                                        <input type="text" wire:model.live="invoiceForm.items.{{ $index }}.product_name" placeholder="ej. Zapatos Nike Air Max"
                                               class="mt-1 w-full rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs focus:border-emerald-500">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-medium text-gray-500">{{ __('Tienda / Proveedor') }}</label>
                                        <input type="text" wire:model.live="invoiceForm.items.{{ $index }}.store" placeholder="ej. Amazon / Nike"
                                               class="mt-1 w-full rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs focus:border-emerald-500">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-medium text-gray-500">{{ __('Cant.') }}</label>
                                        <input type="number" min="1" wire:model.live="invoiceForm.items.{{ $index }}.quantity"
                                               class="mt-1 w-full rounded-lg border border-gray-300 px-2 py-1.5 text-xs text-center focus:border-emerald-500">
                                    </div>
                                    <div class="sm:col-span-2 relative">
                                        <label class="block text-[11px] font-medium text-gray-500">{{ __('Precio ($)') }}</label>
                                        <div class="flex items-center gap-1 mt-1">
                                            <input type="number" step="0.01" min="0" wire:model.live="invoiceForm.items.{{ $index }}.unit_price"
                                                   class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-xs focus:border-emerald-500">
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

                        {{-- Additional Fees / Costs --}}
                        <div class="mt-4 border-t border-gray-200 pt-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-700">{{ __('Cargos / Comisiones Adicionales') }}</span>
                                <button type="button" wire:click="addCost" class="text-xs text-emerald-600 hover:underline">
                                    + {{ __('Agregar Cargo') }}
                                </button>
                            </div>

                            <div class="space-y-2">
                                @foreach ($invoiceForm['costs'] as $cIdx => $cost)
                                    <div class="grid gap-2 grid-cols-12 items-center bg-white p-2 rounded-lg border border-gray-200">
                                        <div class="col-span-4">
                                            <select wire:model.live="invoiceForm.costs.{{ $cIdx }}.type" class="w-full rounded-lg border border-gray-300 p-1.5 text-xs">
                                                <option value="personal_shopper">{{ __('Personal Shopper') }}</option>
                                                <option value="repacking">{{ __('Reempaque / Caja') }}</option>
                                                <option value="shipping">{{ __('Envío / Flete') }}</option>
                                                <option value="storage">{{ __('Almacenaje') }}</option>
                                                <option value="other">{{ __('Otro Cargo') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-span-5">
                                            <input type="text" wire:model.live="invoiceForm.costs.{{ $cIdx }}.description" placeholder="{{ __('Descripción') }}"
                                                   class="w-full rounded-lg border border-gray-300 p-1.5 text-xs">
                                        </div>
                                        <div class="col-span-3 flex items-center gap-1">
                                            <input type="number" step="0.01" min="0" wire:model.live="invoiceForm.costs.{{ $cIdx }}.amount" placeholder="$ 0.00"
                                                   class="w-full rounded-lg border border-gray-300 p-1.5 text-xs text-end font-semibold">
                                            <button type="button" wire:click="removeCost({{ $cIdx }})" class="text-red-400 hover:text-red-600 p-1">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 3. Datos del Pago (Lo que paga) --}}
                    <div class="rounded-xl border border-gray-200/80 bg-emerald-50/40 p-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-3 flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">3</span>
                            {{ __('Datos de Pago y Cobro') }}
                        </h4>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-xl bg-white border border-emerald-200 p-3">
                                <span class="text-[11px] font-semibold text-gray-500 uppercase">{{ __('Total Facturado') }}</span>
                                <p class="text-xl font-bold text-gray-900 mt-0.5">{{ money($this->invoicedTotal) }}</p>
                            </div>

                            <div class="rounded-xl bg-white border border-emerald-200 p-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-gray-500 uppercase">{{ __('Monto Pagado ($)') }}</span>
                                    <button type="button" wire:click="payFullAmount" class="text-[11px] text-emerald-700 font-bold hover:underline">
                                        {{ __('Total') }}
                                    </button>
                                </div>
                                <input type="number" step="0.01" min="0" wire:model.live="invoiceForm.amount_paid"
                                       class="mt-1 w-full rounded-lg border border-emerald-400 px-2 py-1 text-sm font-bold text-emerald-700 focus:ring-emerald-500">
                            </div>

                            <div class="rounded-xl bg-white border border-emerald-200 p-3">
                                <span class="text-[11px] font-semibold text-gray-500 uppercase">{{ __('Saldo Restante') }}</span>
                                <p class="text-xl font-bold mt-0.5 {{ $this->pendingBalance > 0 ? 'text-amber-600' : 'text-emerald-700' }}">
                                    {{ money($this->pendingBalance) }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Método de Pago') }}</label>
                                <select wire:model="invoiceForm.payment_method" class="w-full rounded-lg border border-gray-300 bg-white p-2 text-xs">
                                    <option value="zelle">Zelle</option>
                                    <option value="cash">{{ __('Efectivo (Cash)') }}</option>
                                    <option value="card">{{ __('Tarjeta de Crédito/Débito') }}</option>
                                    <option value="transfer">{{ __('Transferencia Bancaria') }}</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="other">{{ __('Otro') }}</option>
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
                        <button type="button" wire:click="closeCreateModal"
                                class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>{{ __('Guardar y Emitir Factura') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
                        <p><strong>Factura:</strong> {{ $paymentForm['request_number'] ?? '' }}</p>
                        <p><strong>Cliente:</strong> {{ $paymentForm['customer_name'] ?? '' }}</p>
                        <p><strong>Total Factura:</strong> {{ money($paymentForm['invoice_total'] ?? 0) }}</p>
                        <p><strong>Ya Pagado:</strong> {{ money($paymentForm['already_paid'] ?? 0) }}</p>
                        <p class="text-amber-700 font-bold"><strong>Saldo Pendiente:</strong> {{ money($paymentForm['pending_balance'] ?? 0) }}</p>
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
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                            <option value="other">Otro</option>
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
