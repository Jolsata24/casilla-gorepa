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
<body class="font-sans text-gray-900 antialiased bg-gray-100">
    
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#f3f4f6]">
        
        <div class="mb-6">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div class="w-full sm:max-w-4xl mt-6 px-8 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
            
            <h2 class="text-2xl font-black text-gray-800 text-center mb-2">Solicitud de Casilla Electrónica</h2>
            <p class="text-gray-500 text-center text-sm mb-8">
                Ingrese su DNI y presione "Buscar" para validar sus datos automáticamente.
            </p>

            {{-- 
               TRUCO PARA EVITAR ERRORES EN EL EDITOR:
               Pasamos los datos de PHP a atributos HTML data-*. 
               Así el JavaScript de abajo no tiene PHP mezclado.
            --}}
            <div id="server-data" 
                 data-status="{{ session('status') }}" 
                 data-error="{{ session('error') }}"
                 data-errors="{{ json_encode($errors->all()) }}"
                 class="hidden"></div>

            <form method="POST" action="{{ route('solicitud.store') }}" id="formSolicitud" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf

                {{-- CAMPOS OCULTOS PARA DIRECCIÓN (Se llenan con JS) --}}
                <input type="hidden" name="departamento" id="departamento">
                <input type="hidden" name="provincia" id="provincia">
                <input type="hidden" name="distrito" id="distrito">
                <input type="hidden" name="direccion" id="direccion">

                {{-- DNI --}}
                <div class="md:col-span-1">
                    <x-input-label for="dni" :value="__('Número de DNI')" />
                    <div class="flex gap-2 mt-1">
                        <x-text-input id="dni" class="block w-full" type="text" name="dni" :value="old('dni')" required autofocus maxlength="8" placeholder="Ej: 40338..." />
                        
                        <button type="button" onclick="buscarDni()" id="btn_buscar"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center transition-all shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                    <p id="mensaje_dni" class="text-xs mt-1 font-bold"></p>
                </div>

                {{-- NOMBRES Y APELLIDOS --}}
                <div class="md:col-span-1">
                    <x-input-label for="name" :value="__('Nombres')" />
                    <x-text-input id="name" class="block mt-1 w-full bg-gray-50" type="text" name="name" :value="old('name')" required readonly />
                </div>
                <div>
                    <x-input-label for="apellido_paterno" :value="__('Apellido Paterno')" />
                    <x-text-input id="apellido_paterno" class="block mt-1 w-full bg-gray-50" type="text" name="apellido_paterno" :value="old('apellido_paterno')" required readonly />
                </div>
                <div>
                    <x-input-label for="apellido_materno" :value="__('Apellido Materno')" />
                    <x-text-input id="apellido_materno" class="block mt-1 w-full bg-gray-50" type="text" name="apellido_materno" :value="old('apellido_materno')" required readonly />
                </div>

                <div class="md:col-span-2 border-b border-gray-100 pb-4 mb-2 mt-4">
                    <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider">2. Datos de Contacto</h3>
                </div>

                {{-- EMAIL Y CELULAR --}}
                <div class="md:col-span-1">
                    <x-input-label for="email" :value="__('Correo Electrónico')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="ejemplo@gmail.com" />
                </div>
                <div class="md:col-span-1">
                    <x-input-label for="celular" :value="__('Número de Celular')" />
                    <x-text-input id="celular" class="block mt-1 w-full" type="text" name="celular" :value="old('celular')" required placeholder="Ej: 987654321" />
                </div>

                <div class="md:col-span-2 flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                    <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('login') }}">
                        {{ __('¿Ya tienes cuenta? Iniciar Sesión') }}
                    </a>
                    <x-primary-button id="btnEnviar" class="px-8 py-3 text-base">
                        {{ __('ENVIAR SOLICITUD') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
        <div class="mt-8 text-center text-xs text-gray-400 pb-8">&copy; {{ date('Y') }} Gobierno Regional de Pasco.</div>
    </div>

    {{-- JAVASCRIPT LIMPIO (Sin PHP inyectado para evitar errores de editor) --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Leemos los datos desde el div oculto "server-data"
        const dataDiv = document.getElementById('server-data');
        const status = dataDiv.dataset.status;
        const error = dataDiv.dataset.error;
        // Parseamos los errores de validación (vienen como string JSON)
        let errors = [];
        try {
            errors = JSON.parse(dataDiv.dataset.errors || '[]');
        } catch (e) { errors = []; }

        // 1. Alerta de Éxito
        if (status) {
            Swal.fire({
                title: '¡Solicitud Registrada!',
                text: status,
                icon: 'success',
                confirmButtonColor: '#2563EB',
                allowOutsideClick: false
            });
        }

        // 2. Alerta de Error General
        if (error) {
            Swal.fire({
                title: 'Error',
                text: error,
                icon: 'error',
                confirmButtonColor: '#EF4444'
            });
        }

        // 3. Alerta de Errores de Validación (ej. DNI repetido)
        if (errors.length > 0) {
            let lista = '<ul style="text-align: left; font-size: 0.9em; margin-top: 10px;">';
            errors.forEach(err => lista += `<li>• ${err}</li>`);
            lista += '</ul>';
            
            Swal.fire({
                title: 'Revise los datos',
                html: lista,
                icon: 'warning',
                confirmButtonText: 'Corregir',
                confirmButtonColor: '#F59E0B'
            });
        }
    });

    // Bloquear botón al enviar
    const form = document.getElementById('formSolicitud');
    if(form) {
        form.onsubmit = function() {
            const btn = document.getElementById('btnEnviar');
            btn.disabled = true;
            btn.innerText = 'ENVIANDO...';
        };
    }

    // Buscar DNI
    async function buscarDni() {
        const dni = document.getElementById('dni').value;
        const btn = document.getElementById('btn_buscar');
        const msj = document.getElementById('mensaje_dni');

        if (dni.length !== 8) {
            msj.innerText = "El DNI debe tener 8 dígitos.";
            msj.className = "text-xs mt-1 font-bold text-red-500";
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
        msj.innerText = "Consultando RENIEC...";
        msj.className = "text-xs mt-1 font-bold text-blue-500";

        try {
            const response = await fetch(`/dni/info/${dni}`);
            const data = await response.json();

            if (data.success) {
                // Llenar visibles
                document.getElementById('name').value = data.data.nombres;
                document.getElementById('apellido_paterno').value = data.data.apellido_paterno;
                document.getElementById('apellido_materno').value = data.data.apellido_materno;
                
                // Llenar ocultos (importante para que se guarde la dirección)
                document.getElementById('departamento').value = data.data.departamento || '';
                document.getElementById('provincia').value = data.data.provincia || '';
                document.getElementById('distrito').value = data.data.distrito || '';
                document.getElementById('direccion').value = data.data.direccion || '';

                msj.innerText = "¡Datos encontrados!";
                msj.className = "text-xs mt-1 font-bold text-green-600";
            } else {
                msj.innerText = "No encontrado. Llene manualmente.";
                msj.className = "text-xs mt-1 font-bold text-red-500";
                limpiarCampos();
            }
        } catch (error) {
            console.error(error);
            msj.innerText = "Error de conexión. Llene manualmente.";
            msj.className = "text-xs mt-1 font-bold text-red-500";
            document.querySelectorAll('#name, #apellido_paterno, #apellido_materno').forEach(el => el.readOnly = false);
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>`;
        }
    }

    function limpiarCampos() {
        ['name','apellido_paterno','apellido_materno','departamento','provincia','distrito','direccion'].forEach(id => {
            document.getElementById(id).value = '';
        });
    }
    </script>
</body>
</html>