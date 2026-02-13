<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitar Acceso - GORE PASCO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100 flex items-center justify-center min-h-screen py-10">
    
    <div class="w-full sm:max-w-3xl px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-2xl border border-gray-200">
        
        <div class="flex justify-center mb-6">
            <x-application-logo class="w-24 h-24 fill-current text-gray-500" />
        </div>

        <h2 class="text-3xl font-extrabold text-gray-800 text-center mb-2">Solicitud de Casilla Electrónica</h2>
        <p class="text-gray-500 text-center text-sm mb-8 px-8">
            Complete sus datos para solicitar acceso. Su cuenta deberá ser aprobada por un administrador antes de poder ingresar.
        </p>

        @if (session('status'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({ title: '¡Enviado!', text: "{{ session('status') }}", icon: 'success', confirmButtonColor: '#1F2937' });
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({ title: 'Error', text: "{{ session('error') }}", icon: 'error', confirmButtonColor: '#EF4444' });
                });
            </script>
        @endif

        <form method="POST" action="{{ route('solicitud.store') }}" id="formSolicitud" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            <input type="hidden" name="departamento" id="departamento">
            <input type="hidden" name="provincia" id="provincia">
            <input type="hidden" name="distrito" id="distrito">
            <input type="hidden" name="direccion" id="direccion">

            <div class="md:col-span-1">
                <x-input-label for="dni" :value="__('DNI')" />
                <div class="flex gap-2 mt-1">
                    <x-text-input id="dni" class="block w-full" type="text" name="dni" :value="old('dni')" required autofocus maxlength="8" placeholder="Ingrese DNI" />
                    <button type="button" id="btn-validar-dni" class="px-4 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                        Validar
                    </button>
                </div>
                <x-input-error :messages="$errors->get('dni')" class="mt-2" />
            </div>

            <div class="md:col-span-1">
                <x-input-label for="name" :value="__('Nombres')" />
                <x-text-input id="name" class="block mt-1 w-full bg-gray-100 cursor-not-allowed" type="text" name="name" :value="old('name')" required readonly />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="apellido_paterno" :value="__('Apellido Paterno')" />
                <x-text-input id="apellido_paterno" class="block mt-1 w-full bg-gray-100 cursor-not-allowed" type="text" name="apellido_paterno" :value="old('apellido_paterno')" required readonly />
            </div>
            <div>
                <x-input-label for="apellido_materno" :value="__('Apellido Materno')" />
                <x-text-input id="apellido_materno" class="block mt-1 w-full bg-gray-100 cursor-not-allowed" type="text" name="apellido_materno" :value="old('apellido_materno')" required readonly />
            </div>

            <div class="md:col-span-2 mt-2 p-4 bg-gray-50 rounded-lg border border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 mb-2">Datos de Ubicación (Automático)</h3>
                <div class="grid grid-cols-3 gap-4">
                    <input type="text" id="view_departamento" class="text-xs w-full bg-transparent border-none p-0 text-gray-500" placeholder="Departamento" readonly>
                    <input type="text" id="view_provincia" class="text-xs w-full bg-transparent border-none p-0 text-gray-500" placeholder="Provincia" readonly>
                    <input type="text" id="view_distrito" class="text-xs w-full bg-transparent border-none p-0 text-gray-500" placeholder="Distrito" readonly>
                </div>
                <input type="text" id="view_direccion" class="text-xs w-full bg-transparent border-none p-0 mt-1 text-gray-500" placeholder="Dirección completa..." readonly>
            </div>

            <div class="md:col-span-2 border-t border-gray-100 my-2"></div>

            <div class="md:col-span-1">
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="ejemplo@gmail.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="md:col-span-1">
                <x-input-label for="celular" :value="__('Celular / Teléfono')" />
                <x-text-input id="celular" class="block mt-1 w-full" type="text" name="celular" :value="old('celular')" required placeholder="999 999 999" />
                <x-input-error :messages="$errors->get('celular')" class="mt-2" />
            </div>

            <div class="md:col-span-2 flex items-center justify-between mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('¿Ya tienes cuenta? Iniciar Sesión') }}
                </a>

                <x-primary-button id="btnEnviar" class="px-8 py-3">
                    {{ __('ENVIAR SOLICITUD') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('btn-validar-dni').addEventListener('click', async () => {
            const dni = document.getElementById('dni').value;
            const btn = document.getElementById('btn-validar-dni');

            if (dni.length !== 8) return Swal.fire('Error', 'El DNI debe tener 8 dígitos', 'warning');

            btn.disabled = true;
            btn.innerText = '...';

            try {
                const response = await fetch(`/dni/info/${dni}`);
                const result = await response.json();

                if (result.success) {
                    const info = result.data;
                    
                    // Llenar inputs visibles
                    document.getElementById('name').value = info.nombres;
                    document.getElementById('apellido_paterno').value = info.apellido_paterno;
                    document.getElementById('apellido_materno').value = info.apellido_materno;

                    // Llenar inputs ocultos (Para enviar a BD)
                    document.getElementById('departamento').value = info.departamento;
                    document.getElementById('provincia').value = info.provincia;
                    document.getElementById('distrito').value = info.distrito;
                    document.getElementById('direccion').value = info.direccion;

                    // Llenar visualización (Solo lectura)
                    document.getElementById('view_departamento').value = info.departamento;
                    document.getElementById('view_provincia').value = info.provincia;
                    document.getElementById('view_distrito').value = info.distrito;
                    document.getElementById('view_direccion').value = info.direccion;
                } else {
                    Swal.fire('No encontrado', 'DNI no encontrado en RENIEC', 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Error de conexión', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Validar';
            }
        });

        // Prevenir doble envío
        document.getElementById('formSolicitud').addEventListener('submit', function() {
            const btn = document.getElementById('btnEnviar');
            btn.disabled = true;
            btn.innerText = 'ENVIANDO...';
        });
    </script>
</body>
</html>