@unless (request()->routeIs('request'))
    <a href="{{ route('request') }}"
       wire:navigate
       class="fixed bottom-24 end-4 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white shadow-xl shadow-emerald-300 transition-all duration-200 hover:scale-110 hover:bg-emerald-700 md:bottom-6 md:end-6"
       aria-label="{{ __('Chat with us') }}"
       title="{{ __('Chat with us') }}">
        <i class="fa-solid fa-comments text-2xl"></i>
    </a>
@endunless
