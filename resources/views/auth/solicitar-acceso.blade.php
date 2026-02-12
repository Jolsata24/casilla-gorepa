<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitar Acceso - GORE PASCO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">
    
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#f3f4f6]">
        
        {{-- LOGO (Opcional) --}}
        <div class="mb-6">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        {{-- TARJETA PRINCIPAL (Aquí ajustamos el ancho a 'max-w-4xl' para que sea más grande) --}}
        <div class="w-full sm:max-w-4xl mt-6 px-8 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
            
            <h2 class="text-2xl font-black text-gray-800 text-center mb-2">Solicitud de Casilla Electrónica</h2>
            <p class="text-gray-500 text-center text-sm mb-8">
                Ingrese su DNI y presione "Buscar" para validar sus datos automáticamente.
            </p>

            {{-- Mensajes de Error/Éxito --}}
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-lg border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('solicitud.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf

                {{-- SECCIÓN 1: IDENTIFICACIÓN --}}
                <div class="md:col-span-2 border-b border-gray-100 pb-4 mb-2">
                    <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider">1. Identificación del Solicitante</h3>
                </div>

                {{-- DNI + BOTÓN BUSCAR --}}
                <div class="md:col-span-1">
                    <x-input-label for="dni" :value="__('Número de DNI')" />
                    <div class="flex gap-2 mt-1">
                        <x-text-input id="dni" class="block w-full" type="text" name="dni" :value="old('dni')" required autofocus maxlength="8" placeholder="Ej: 40338..." />
                        
                        <button type="button" onclick="buscarDni()" id="btn_buscar"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                    <p id="mensaje_dni" class="text-xs mt-1 font-bold"></p>
                    <x-input-error :messages="$errors->get('dni')" class="mt-2" />
                </div>

                {{-- NOMBRES --}}
                <div class="md:col-span-1">
                    <x-input-label for="name" :value="__('Nombres')" />
                    <x-text-input id="name" class="block mt-1 w-full bg-gray-50" type="text" name="name" :value="old('name')" required readonly />
                </div>

                {{-- APELLIDO PATERNO --}}
                <div>
                    <x-input-label for="apellido_paterno" :value="__('Apellido Paterno')" />
                    <x-text-input id="apellido_paterno" class="block mt-1 w-full bg-gray-50" type="text" name="apellido_paterno" :value="old('apellido_paterno')" required readonly />
                </div>

                {{-- APELLIDO MATERNO --}}
                <div>
                    <x-input-label for="apellido_materno" :value="__('Apellido Materno')" />
                    <x-text-input id="apellido_materno" class="block mt-1 w-full bg-gray-50" type="text" name="apellido_materno" :value="old('apellido_materno')" required readonly />
                </div>

                {{-- SECCIÓN 2: CONTACTO --}}
                <div class="md:col-span-2 border-b border-gray-100 pb-4 mb-2 mt-4">
                    <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider">2. Datos de Contacto</h3>
                </div>

                {{-- CORREO --}}
                <div class="md:col-span-1">
                    <x-input-label for="email" :value="__('Correo Electrónico (Personal)')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="ejemplo@gmail.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- CELULAR (Opcional si lo tienes en BD) --}}
                <div class="md:col-span-1">
                    <x-input-label for="celular" :value="__('Número de Celular')" />
                    <x-text-input id="celular" class="block mt-1 w-full" type="text" name="celular" :value="old('celular')" placeholder="Ej: 987654321" />
                </div>

                {{-- BOTONES --}}
                <div class="md:col-span-2 flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                    <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('login') }}">
                        {{ __('¿Ya tienes cuenta? Iniciar Sesión') }}
                    </a>

                    <x-primary-button class="px-8 py-3 text-base">
                        {{ __('ENVIAR SOLICITUD') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="mt-8 text-center text-xs text-gray-400 pb-8">
            &copy; {{ date('Y') }} Gobierno Regional de Pasco. Todos los derechos reservados.
        </div>
    </div>

    {{-- SCRIPT PARA CONSUMIR LA API DE DNI --}}
    <script>
        async function buscarDni() {
            const dni = document.getElementById('dni').value;
            const btn = document.getElementById('btn_buscar');
            const mensaje = document.getElementById('mensaje_dni');

            // Validar longitud
            if (dni.length !== 8) {
                mensaje.innerText = "El DNI debe tener 8 dígitos.";
                mensaje.className = "text-xs mt-1 font-bold text-red-500";
                return;
            }

            // UI: Cargando...
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            mensaje.innerText = "Consultando RENIEC...";
            mensaje.className = "text-xs mt-1 font-bold text-blue-500";

            try {
                // Hacemos la petición a TU ruta interna (definida en web.php)
                const response = await fetch(`/dni/info/${dni}`);
                const data = await response.json();

                if (data.success) {
                    // Rellenar campos
                    document.getElementById('name').value = data.data.nombres;
                    document.getElementById('apellido_paterno').value = data.data.apellido_paterno;
                    document.getElementById('apellido_materno').value = data.data.apellido_materno;

                    // UI: Éxito
                    mensaje.innerText = "¡Datos encontrados!";
                    mensaje.className = "text-xs mt-1 font-bold text-green-600";
                } else {
                    mensaje.innerText = "No se encontraron datos para este DNI.";
                    mensaje.className = "text-xs mt-1 font-bold text-red-500";
                    limpiarCampos();
                }

            } catch (error) {
                console.error(error);
                mensaje.innerText = "Error al consultar. Intente manualmente.";
                mensaje.className = "text-xs mt-1 font-bold text-red-500";
                
                // Permitir escritura manual si falla la API
                document.getElementById('name').readOnly = false;
                document.getElementById('apellido_paterno').readOnly = false;
                document.getElementById('apellido_materno').readOnly = false;
            } finally {
                // Restaurar botón
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>`;
            }
        }

        function limpiarCampos() {
            document.getElementById('name').value = '';
            document.getElementById('apellido_paterno').value = '';
            document.getElementById('apellido_materno').value = '';
        }
    </script>
</body>
</html>