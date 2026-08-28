@php
    $waPhone = preg_replace('/\D+/', '', \App\Models\Setting::get('whatsapp_phone', '13462333199'));
    $waMsg = urlencode(__('¡Hola! Quisiera solicitar información sobre sus servicios de compras y envíos.'));
@endphp

<a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}"
   target="_blank"
   rel="noopener noreferrer"
   class="fixed bottom-20 end-4 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-xl shadow-green-600/40 transition-all duration-300 hover:scale-110 hover:bg-[#20ba59] hover:shadow-2xl hover:shadow-green-600/50 md:bottom-6 md:end-6 group"
   aria-label="{{ __('Chat on WhatsApp') }}"
   title="{{ __('Chat on WhatsApp') }}">
    <span class="absolute -top-1 -end-1 flex h-3.5 w-3.5">
        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span>
        <span class="relative inline-flex h-3.5 w-3.5 rounded-full bg-white border-2 border-[#25D366]"></span>
    </span>
    <!-- Official WhatsApp Solid White SVG Icon -->
    <svg class="h-8 w-8 text-white transition-transform duration-300 group-hover:scale-110" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2C8.268 2 2 8.268 2 16c0 2.766.804 5.344 2.188 7.516L2.2 30.2l6.906-1.954C11.125 29.375 13.484 30 16 30c7.732 0 14-6.268 14-14S23.732 2 16 2zm0 25.556c-2.222 0-4.306-.597-6.111-1.639l-.444-.25-4.542 1.292 1.292-4.417-.278-.444A11.472 11.472 0 014.444 16C4.444 9.625 9.625 4.444 16 4.444S27.556 9.625 27.556 16 22.375 27.556 16 27.556zm6.306-8.583c-.347-.18-2.056-1.014-2.375-1.125-.319-.125-.556-.18-.792.18-.236.361-.917 1.125-1.125 1.361-.208.236-.417.264-.764.097-.347-.18-1.472-.542-2.806-1.722-1.042-.931-1.736-2.083-1.944-2.444-.208-.361-.028-.556.153-.722.153-.153.347-.403.528-.611.18-.208.236-.361.361-.597.125-.236.056-.444-.028-.625-.083-.18-.792-1.903-1.083-2.611-.292-.694-.583-.597-.792-.611-.208-.014-.444-.014-.681-.014-.236 0-.625.083-.944.444-.319.361-1.222 1.222-1.222 2.972 0 1.75 1.278 3.444 1.444 3.681.18.236 2.514 3.847 6.097 5.389.85.367 1.514.586 2.03.75.854.272 1.63.233 2.244.142.686-.103 2.111-.861 2.408-1.694.297-.833.297-1.542.208-1.694-.09-.153-.326-.236-.673-.417z" fill="#FFFFFF"/>
    </svg>
</a>
