<div>
    <x-slot name="header">{{ __('My Packages') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Photo') }}</th>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Store') }}</th>
                        <th class="px-4 py-3">{{ __('Original Tracking') }}</th>
                        <th class="px-4 py-3">{{ __('Received At') }}</th>
                        <th class="px-4 py-3">{{ __('Weight (lb)') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($packages as $package)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if ($package->photo_path)
                                    <img src="{{ asset('storage/' . $package->photo_path) }}" alt="{{ __('Photo of package') }}"
                                         data-lightbox="{{ asset('storage/' . $package->photo_path) }}"
                                         class="h-14 w-14 cursor-zoom-in rounded-lg border border-gray-200 object-cover">
                                @else
                                    <span class="flex h-14 w-14 items-center justify-center rounded-lg bg-gray-50 text-gray-300">
                                        <i class="fa-solid fa-box text-xl"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $package->number }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $package->store ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $package->original_tracking ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $package->received_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $package->weight_lb ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$package->status" /></td>
                        </tr>
                    @empty
                        <tr>
    <td colspan="99">
        <x-empty-state :message="__('No records found.')" icon="box" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($packages as $package)
                <li class="flex items-center gap-3 px-4 py-4">
                    @if ($package->photo_path)
                        <img src="{{ asset('storage/' . $package->photo_path) }}" alt="{{ __('Photo of package') }}"
                             data-lightbox="{{ asset('storage/' . $package->photo_path) }}"
                             class="h-12 w-12 shrink-0 cursor-zoom-in rounded-lg border border-gray-200 object-cover">
                    @else
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-50 text-gray-300">
                            <i class="fa-solid fa-box text-xl"></i>
                        </span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-3">
                            <p class="truncate font-mono text-xs text-gray-900">{{ $package->number }}</p>
                            <x-status-badge :status="$package->status" />
                        </div>
                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $package->store ?? __('Package') }}</p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-400">
                            @if ($package->original_tracking)
                                <span class="truncate font-mono">{{ $package->original_tracking }}</span>
                            @endif
                            <span>{{ $package->received_at?->format('Y-m-d') ?? '—' }}</span>
                            @if ($package->weight_lb)
                                <span>{{ $package->weight_lb }} lb</span>
                            @endif
                        </div>
                    </div>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No records found.')" icon="box" />
                </li>
            @endforelse
        </ul>

        <div class="border-t border-gray-200 p-4">
            {{ $packages->links() }}
        </div>
    </div>
</div>
