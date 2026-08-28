@props(['items' => []])

@if (count($items))
    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200/80 bg-white/95 backdrop-blur-xl shadow-[0_-4px_20px_rgba(0,0,0,0.06)] md:hidden print:hidden"
         aria-label="{{ __('Mobile navigation') }}">
        <div class="no-scrollbar flex items-stretch overflow-x-auto">
            @foreach ($items as $item)
                @php $active = ! empty($item['active']); @endphp
                <a href="{{ $item['url'] }}"
                   @if (! empty($item['navigate'])) wire:navigate @endif
                   class="group relative flex min-w-[72px] flex-1 flex-col items-center justify-center gap-1 px-2 pb-2 pt-2.5 text-[10px] font-semibold transition-colors {{ $active ? 'text-emerald-700' : 'text-gray-500 hover:text-gray-800' }}">
                    <span class="flex h-8 w-14 items-center justify-center rounded-full transition-all duration-200 {{ $active ? 'bg-emerald-100/80 text-emerald-600 shadow-[0_2px_8px_rgba(16,185,129,0.25)]' : 'text-gray-400 group-hover:text-gray-600' }}">
                        <i class="{{ $item['icon'] }} text-xl leading-none"></i>
                    </span>
                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
        <div class="h-[env(safe-area-inset-bottom)] bg-white"></div>
    </nav>
@endif
