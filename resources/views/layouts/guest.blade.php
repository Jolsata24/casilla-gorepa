<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'GORE Pasco') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-gray-900 relative bg-gray-900 overflow-hidden">
        {{-- FONDO NATURAL --}}
        <div class="fixed inset-0 z-0">
            <img src="{{ asset('fondo-gorepa.jpg') }}" alt="Fondo" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/40"></div>
        </div>

        <div class="relative z-10 flex flex-col min-h-screen">
            <header class="bg-white/90 backdrop-blur-md border-b border-white/20 h-20 shadow-sm flex-none">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('logo-gorepa.png') }}" alt="GORE Pasco" class="h-12 w-auto object-contain">
                        <div class="hidden md:block h-8 w-px bg-gray-400"></div>
                        <h1 class="hidden md:block text-sm font-bold text-gray-800 uppercase tracking-wide leading-tight">
                            Gobierno Regional<br><span class="text-gorepa-600">De Pasco</span>
                        </h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('gob.png') }}" alt="GORE Pasco" class="h-12 w-auto object-contain">
                        <div class="hidden md:block h-8 w-px bg-gray-400"></div>
                        <h1 class="hidden md:block text-sm font-bold text-gray-800 uppercase tracking-wide leading-tight">
                            Gobierno Regional<br><span class="text-gorepa-600">De Pasco</span>
                        </h1>
                    </div>
                </div>
            </header>

            <main class="flex-1 flex items-center justify-center p-4">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>