<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#f3f4f6]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-4">
            {{-- Contenedor principal más ancho para el diseño dividido --}}
            <div class="w-full sm:max-w-4xl bg-white shadow-[0_20px_60px_rgba(0,0,0,0.08)] overflow-hidden sm:rounded-3xl flex flex-col md:flex-row min-h-[600px]">
                {{ $slot }}
            </div>
            
            <p class="mt-6 text-xs text-gray-400 font-medium tracking-widest uppercase">
                Gobierno Regional de Pasco
            </p>
        </div>
    </body>
</html>