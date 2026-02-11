<x-guest-layout>
    {{-- LADO IZQUIERDO: Formulario de Acceso --}}
    <div class="w-full md:w-1/2 p-10 lg:p-16 flex flex-col justify-center relative z-10 bg-white md:rounded-l-[2.5rem]">
        <div class="mb-10 text-center md:text-left">
            <div class="flex justify-center md:justify-start mb-6">
                <img src="{{ asset('logo-gorepa.png') }}" alt="Logo GOREPA" class="h-20 w-auto object-contain">
            </div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Bienvenido</h2>
            <p class="text-gray-500 mt-2 font-medium">Ingresa tus credenciales para acceder a la Casilla Electrónica.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            {{-- Campos de Email y Password (sin cambios) --}}
            <div>
                <x-input-label for="email" :value="__('Correo Institucional')" class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 ml-1" />
                <x-text-input id="email" class="block w-full border-gray-200 bg-gray-50/50 focus:bg-white focus:border-gorepa-500 focus:ring-gorepa-500 rounded-2xl py-3 transition-all" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Contraseña')" class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 ml-1" />
                <x-text-input id="password" class="block w-full border-gray-200 bg-gray-50/50 focus:bg-white focus:border-gorepa-500 focus:ring-gorepa-500 rounded-2xl py-3 transition-all" type="password" name="password" required />
            </div>

            <div class="flex items-center justify-between px-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded-md border-gray-300 text-gorepa-500 focus:ring-gorepa-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Mantenerme conectado</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm text-gorepa-600 hover:text-gorepa-700 font-bold" href="{{ route('password.request') }}">¿Olvidaste tu clave?</a>
                @endif
            </div>

            {{-- BOTÓN PRINCIPAL --}}
            <button type="submit" class="w-full py-4 bg-gorepa-500 hover:bg-gorepa-600 text-white rounded-2xl font-bold uppercase tracking-widest shadow-xl shadow-gorepa-500/25 transition-all transform active:scale-95">
                Iniciar Sesión
            </button>

            {{-- NUEVA SECCIÓN: SOLICITUD DE CITA --}}
            <div class="pt-6 border-t border-gray-100">
                <div class="text-center mb-4">
                    <p class="text-gray-500 text-sm font-semibold italic">¿Primera vez aquí?</p>
                </div>
                
                <a href="{{ route('solicitud.create') }}" class="w-full inline-flex justify-center items-center px-4 py-3 border-2 border-gorepa-500 text-gorepa-600 hover:bg-gorepa-50 rounded-2xl font-bold text-sm uppercase tracking-tight transition-all duration-200 text-center">
                    Solicite una cita para obtener usuario y clave
                </a>
            </div>
        </form>
    </div>

    {{-- LADO DERECHO: Imagen de Fondo GOREPA (Mismo diseño anterior) --}}
    <div class="hidden md:flex md:w-1/2 relative items-center justify-center p-12 overflow-hidden md:rounded-r-[2.5rem]">
        <img src="{{ asset('fondo-gorepa.jpg') }}" alt="Sede GORE Pasco" class="absolute inset-0 w-full h-full object-cover scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-gorepa-600/90 to-gorepa-500/80 mix-blend-multiply"></div>
        
        <div class="relative z-10 text-center">
            <div class="inline-block mb-6">
                 <img src="{{ asset('logo-gorepa.png') }}" alt="GOREPA" class="h-28 w-auto brightness-0 invert drop-shadow-lg">
            </div>
            <h1 class="text-4xl font-black text-white mb-4 tracking-tight drop-shadow-md">GOBIERNO REGIONAL<br>DE PASCO</h1>
            <div class="w-24 h-1.5 bg-white mx-auto rounded-full mb-6 shadow-sm"></div>
            <p class="text-white text-lg font-semibold max-w-md mx-auto leading-relaxed drop-shadow-sm">
                "Trabajando por el desarrollo y la integración de nuestra región."
            </p>
        </div>
    </div>
</x-guest-layout>