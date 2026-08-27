<div>
    <x-slot name="header">{{ __('My Packages') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
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
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                        </svg>
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

        <div class="border-t border-gray-200 p-4">
            {{ $packages->links() }}
        </div>
    </div>
</div>
