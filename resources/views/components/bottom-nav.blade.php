@props(['items' => []])

@if (count($items))
    <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white/95 backdrop-blur-xl shadow-[0_-4px_20px_rgba(0,0,0,0.08)] lg:hidden print:hidden"
         aria-label="{{ __('Mobile navigation') }}">
        <div class="flex items-stretch justify-around px-1 py-1">
            @foreach ($items as $item)
                @php $active = ! empty($item['active']); @endphp
                @if (! empty($item['action']) && $item['action'] === 'open-sidebar')
                    <button type="button"
                            @click="$dispatch('open-sidebar')"
                            class="group relative flex min-w-0 flex-1 flex-col items-center justify-center gap-1 px-1 py-1.5 text-[10px] font-semibold text-gray-500 hover:text-emerald-700 active:scale-95 transition-all">
                        <span class="flex h-7 w-12 items-center justify-center rounded-full transition-all duration-200 text-gray-500 group-hover:text-emerald-600 group-hover:bg-emerald-50">
                            <i class="{{ $item['icon'] }} text-lg leading-none"></i>
                        </span>
                        <span class="truncate max-w-full text-center">{{ $item['label'] }}</span>
                    </button>
                @else
                    <a href="{{ $item['url'] }}"
                       @if (! empty($item['navigate'])) wire:navigate @endif
                       class="group relative flex min-w-0 flex-1 flex-col items-center justify-center gap-1 px-1 py-1.5 text-[10px] font-semibold transition-all {{ $active ? 'text-emerald-700' : 'text-gray-500 hover:text-gray-800' }}">
                        <span class="flex h-7 w-12 items-center justify-center rounded-full transition-all duration-200 {{ $active ? 'bg-emerald-100/90 text-emerald-600 shadow-xs' : 'text-gray-400 group-hover:text-gray-600' }}">
                            <i class="{{ $item['icon'] }} text-lg leading-none"></i>
                        </span>
                        <span class="truncate max-w-full text-center">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
        <div class="h-[env(safe-area-inset-bottom,0px)] bg-white"></div>
    </nav>
@endif

