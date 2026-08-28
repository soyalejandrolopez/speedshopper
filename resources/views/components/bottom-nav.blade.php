@props(['items' => []])

@if (count($items))
    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 backdrop-blur-lg md:hidden print:hidden"
         aria-label="{{ __('Mobile navigation') }}">
        <div class="no-scrollbar flex items-stretch overflow-x-auto">
            @foreach ($items as $item)
                <a href="{{ $item['url'] }}"
                   @if (! empty($item['navigate'])) wire:navigate @endif
                   class="flex min-w-[64px] flex-1 flex-col items-center justify-center gap-1 px-2 py-2 text-[10px] font-semibold transition-colors {{ ! empty($item['active']) ? 'text-emerald-700' : 'text-gray-500 hover:text-gray-800' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ ! empty($item['active']) ? 'bg-emerald-50 text-emerald-600' : 'text-gray-400' }}">
                        <i class="{{ $item['icon'] }} text-xl"></i>
                    </span>
                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
        <div class="h-[env(safe-area-inset-bottom)] bg-white"></div>
    </nav>
@endif

