<x-hamster-ascii />
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <x-theme-color />
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50 flex items-center justify-center min-h-screen relative overflow-hidden">
    <!-- Background Design -->
    <div class="pointer-events-none absolute inset-0 z-0">
        <div class="absolute -top-24 -start-24 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 end-0 h-96 w-96 rounded-full bg-teal-500/10 blur-3xl"></div>
    </div>

    <!-- Error Card -->
    <div class="relative z-10 w-full max-w-lg px-4">
        <div class="glass-panel p-8 md:p-12 text-center rounded-3xl shadow-xl shadow-gray-200/50">
            <!-- Logo -->
            <div class="mx-auto flex justify-center mb-8">
                <a href="{{ url('/') }}" class="inline-block transition-transform duration-300 hover:scale-105">
                    <x-brand-logo size="md" />
                </a>
            </div>

            <!-- Error Content -->
            <div class="space-y-4">
                <h1 class="text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500 tracking-tighter drop-shadow-sm">
                    @yield('code')
                </h1>
                
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    @yield('message')
                </h2>

                <p class="text-gray-500 text-sm max-w-sm mx-auto leading-relaxed mt-2">
                    Lo sentimos, ha ocurrido un error o la página que buscas no se encuentra disponible en este momento.
                </p>
            </div>

            <!-- Actions -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/') }}" class="btn-primary w-full sm:w-auto px-6 py-2.5">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span class="ml-2">Volver al Inicio</span>
                </a>
            </div>
        </div>
        
        <!-- Footer text -->
        <div class="mt-8 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
            <div class="mt-1">
                Powered by <a href="https://www.hamstersoftware.com" target="_blank" class="hover:text-emerald-600 transition-colors">Hamster Software</a>
            </div>
        </div>
    </div>
</body>
</html>
