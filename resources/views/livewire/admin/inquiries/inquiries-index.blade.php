<div>
    <x-slot name="header">{{ __('Mensajes de Contacto') }}</x-slot>

    <!-- Header Stats & Filters -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-gray-900">{{ __('Bandeja de Contactos') }}</h1>
            @if ($unreadCount > 0)
                <span class="rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-800 animate-pulse">
                    {{ $unreadCount }} {{ __('nuevos') }}
                </span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <div class="relative min-w-[220px]">
                <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Buscar por nombre, email...') }}"
                       class="input ps-8 !py-1.5 text-xs">
            </div>

            <select wire:model.live="status" class="input !py-1.5 text-xs !w-auto">
                <option value="">{{ __('Todos los estados') }}</option>
                <option value="unread">{{ __('Nuevos / No leídos') }}</option>
                <option value="read">{{ __('Leídos') }}</option>
                <option value="contacted">{{ __('Atendidos') }}</option>
            </select>
        </div>
    </div>

    <!-- Inquiries Table -->
    <div class="card overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/80 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3.5">{{ __('Remitente') }}</th>
                        <th class="px-4 py-3.5">{{ __('Contacto') }}</th>
                        <th class="px-4 py-3.5">{{ __('Destino') }}</th>
                        <th class="px-4 py-3.5">{{ __('Asunto / Mensaje') }}</th>
                        <th class="px-4 py-3.5">{{ __('Fecha') }}</th>
                        <th class="px-4 py-3.5 text-center">{{ __('Estado') }}</th>
                        <th class="px-5 py-3.5 text-end">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($inquiries as $inquiry)
                        <tr class="transition-colors hover:bg-emerald-50/30 {{ $inquiry->isUnread() ? 'bg-emerald-50/20 font-medium' : '' }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-xs font-bold text-white shadow-sm">
                                        {{ strtoupper(substr($inquiry->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $inquiry->name }}</p>
                                        <p class="text-xs text-gray-400">ID #{{ $inquiry->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                <div>
                                    <a href="mailto:{{ $inquiry->email }}" class="text-emerald-700 hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-envelope text-[11px] text-gray-400"></i>
                                        {{ $inquiry->email }}
                                    </a>
                                </div>
                                @if ($inquiry->phone)
                                    @php $cleanPhone = preg_replace('/\D+/', '', $inquiry->phone); @endphp
                                    <div class="mt-1">
                                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-gray-600 hover:text-emerald-700 flex items-center gap-1">
                                            <i class="fa-brands fa-whatsapp text-[11px] text-emerald-600"></i>
                                            {{ $inquiry->phone }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                @if ($inquiry->country)
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                        {{ country_name($inquiry->country) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs max-w-xs">
                                <p class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $inquiry->subject ?: 'Consulta') }}</p>
                                <p class="truncate text-gray-500 text-[11px]">{{ $inquiry->message }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-xs whitespace-nowrap text-gray-500">
                                {{ $inquiry->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if ($inquiry->status === 'unread')
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                        {{ __('Nuevo') }}
                                    </span>
                                @elseif ($inquiry->status === 'contacted')
                                    <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-semibold text-blue-800">
                                        {{ __('Atendido') }}
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">
                                        {{ __('Leído') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-end whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" wire:click="openInquiry({{ $inquiry->id }})"
                                            class="rounded-lg p-1.5 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800"
                                            title="{{ __('Ver mensaje') }}">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>
                                    @if ($inquiry->phone)
                                        @php $phoneClean = preg_replace('/\D+/', '', $inquiry->phone); @endphp
                                        <a href="https://wa.me/{{ $phoneClean }}?text={{ urlencode('Hola '.$inquiry->name.', te escribimos de '.config('app.name').' respecto a tu mensaje:') }}"
                                           target="_blank"
                                           class="rounded-lg p-1.5 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800"
                                           title="{{ __('Contactar por WhatsApp') }}">
                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                        </a>
                                    @endif
                                    <button type="button" wire:confirm="{{ __('¿Seguro que deseas eliminar este mensaje?') }}"
                                            wire:click="delete({{ $inquiry->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600"
                                            title="{{ __('Eliminar') }}">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-inbox text-4xl mb-2 text-gray-300 block"></i>
                                {{ __('No hay mensajes de contacto registrados.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($inquiries as $inquiry)
                <li class="px-4 py-4 {{ $inquiry->isUnread() ? 'bg-emerald-50/20' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-xs font-bold text-white shadow-sm">
                                {{ strtoupper(substr($inquiry->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-gray-900">{{ $inquiry->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $inquiry->email }}</p>
                            </div>
                        </div>
                        @if ($inquiry->status === 'unread')
                            <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">{{ __('Nuevo') }}</span>
                        @elseif ($inquiry->status === 'contacted')
                            <span class="shrink-0 rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-semibold text-blue-800">{{ __('Atendido') }}</span>
                        @else
                            <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">{{ __('Leído') }}</span>
                        @endif
                    </div>
                    <p class="mt-2 line-clamp-2 text-xs text-gray-600">
                        <span class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $inquiry->subject ?: 'Consulta') }}:</span>
                        {{ $inquiry->message }}
                    </p>
                    <div class="mt-2 flex items-center gap-1.5 text-xs">
                        <span class="text-gray-400">{{ $inquiry->created_at->diffForHumans() }}</span>
                        @if ($inquiry->country)
                            <span class="rounded bg-gray-100 px-1.5 py-0.5 font-medium text-gray-600">{{ country_name($inquiry->country) }}</span>
                        @endif
                        <span class="ms-auto flex items-center gap-1">
                            <button type="button" wire:click="openInquiry({{ $inquiry->id }})"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </button>
                            @if ($inquiry->phone)
                                @php $phoneClean = preg_replace('/\D+/', '', $inquiry->phone); @endphp
                                <a href="https://wa.me/{{ $phoneClean }}?text={{ urlencode('Hola '.$inquiry->name.', te escribimos de '.config('app.name').' respecto a tu mensaje:') }}"
                                   target="_blank"
                                   class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                </a>
                            @endif
                            <button type="button" wire:confirm="{{ __('¿Seguro que deseas eliminar este mensaje?') }}"
                                    wire:click="delete({{ $inquiry->id }})"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </span>
                    </div>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No hay mensajes de contacto registrados.')" icon="inbox" />
                </li>
            @endforelse
        </ul>

        @if ($inquiries->hasPages())
            <div class="border-t border-gray-100 p-4">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>

    <!-- Inquiry Detail Modal -->
    @if ($selectedInquiry)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" wire:click="closeInquiry"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl animate-fade-up">
                <div class="flex items-start justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-bold text-white shadow-md shadow-emerald-200">
                            {{ strtoupper(substr($selectedInquiry->name, 0, 1)) }}
                        </span>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">{{ $selectedInquiry->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $selectedInquiry->created_at->format('d/m/Y H:i A') }} ({{ $selectedInquiry->created_at->diffForHumans() }})</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeInquiry" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="mt-4 space-y-3.5 text-sm">
                    <div class="grid grid-cols-2 gap-3 rounded-xl bg-gray-50 p-3.5">
                        <div>
                            <span class="text-xs font-semibold uppercase text-gray-400">{{ __('Email') }}</span>
                            <a href="mailto:{{ $selectedInquiry->email }}" class="block font-medium text-emerald-700 hover:underline">
                                {{ $selectedInquiry->email }}
                            </a>
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase text-gray-400">{{ __('Teléfono / WhatsApp') }}</span>
                            <p class="font-medium text-gray-800">{{ $selectedInquiry->phone ?: '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase text-gray-400">{{ __('País Destino') }}</span>
                            <p class="font-medium text-gray-800">{{ $selectedInquiry->country ? country_name($selectedInquiry->country) : '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase text-gray-400">{{ __('Asunto') }}</span>
                            <p class="font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $selectedInquiry->subject ?: 'Consulta') }}</p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs font-semibold uppercase text-gray-400">{{ __('Mensaje del Cliente') }}</span>
                        <div class="mt-1.5 rounded-xl border border-gray-200 bg-white p-4 text-sm leading-relaxed text-gray-800">
                            {{ $selectedInquiry->message }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
                    <div class="flex gap-2">
                        @if ($selectedInquiry->phone)
                            @php $cleanPhone = preg_replace('/\D+/', '', $selectedInquiry->phone); @endphp
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('Hola '.$selectedInquiry->name.', te contactamos desde '.config('app.name').':') }}"
                               target="_blank"
                               class="btn-primary !bg-emerald-600 !py-2 text-xs hover:!bg-emerald-700">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                {{ __('WhatsApp') }}
                            </a>
                        @endif
                        <a href="mailto:{{ $selectedInquiry->email }}?subject={{ urlencode('Respuesta a tu consulta en '.config('app.name')) }}"
                           class="btn-soft !py-2 text-xs">
                            <i class="fa-solid fa-envelope text-sm"></i>
                            {{ __('Enviar Email') }}
                        </a>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($selectedInquiry->status !== 'contacted')
                            <button type="button" wire:click="markAsContacted({{ $selectedInquiry->id }})" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-check text-xs me-1"></i>
                                {{ __('Marcar Atendido') }}
                            </button>
                        @endif
                        <button type="button" wire:click="closeInquiry" class="btn-ghost !py-2 text-xs">
                            {{ __('Cerrar') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
