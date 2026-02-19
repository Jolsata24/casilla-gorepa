<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- ENCABEZADO --}}
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">Redactar Notificación</h2>
                <p class="text-gray-500 font-medium">Seleccione al destinatario y adjunte el documento oficial.</p>
            </div>

            {{-- MENSAJES DE ÉXITO --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                {{-- PASO 1: BUSCADOR DE USUARIO --}}
                <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                    <form method="GET" action="{{ route('admin.crear') }}">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">1. Buscar Destinatario</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar por DNI, RUC, Razón Social o Email...">
                            <button type="submit" class="px-6 py-3 bg-gray-800 text-white font-bold rounded-xl hover:bg-black transition-colors">BUSCAR</button>
                        </div>
                    </form>

                    {{-- Resultados de Búsqueda --}}
                    @if(isset($usuarios) && $usuarios->count() > 0)
                        <div class="mt-4 grid gap-2">
                            <p class="text-xs text-gray-500 font-bold uppercase">Resultados encontrados:</p>
                            @foreach($usuarios as $u)
                                @php
                                    // Lógica para diferenciar Persona Natural de Jurídica
                                    $esRUC = $u->tipo_documento === 'RUC';
                                    $nombreMostrar = $esRUC ? $u->razon_social : trim($u->name . ' ' . $u->apellido_paterno);
                                    $docMostrar = $esRUC ? $u->ruc : $u->dni;
                                    $tipoDoc = $esRUC ? 'RUC' : 'DNI';
                                @endphp

                                <div onclick="seleccionarUsuario('{{ $u->id }}', '{{ addslashes($nombreMostrar) }}', '{{ $docMostrar }}', '{{ $tipoDoc }}')" 
                                     class="cursor-pointer p-3 border rounded-xl hover:bg-blue-50 hover:border-blue-300 transition-colors flex justify-between items-center group">
                                    <div>
                                        <p class="font-bold text-gray-800 uppercase">{{ $nombreMostrar }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $tipoDoc }}: {{ $docMostrar }} &bull; {{ $u->email }}</p>
                                    </div>
                                    <span class="text-xs font-bold text-blue-600 bg-blue-100 px-3 py-1 rounded-full group-hover:bg-blue-600 group-hover:text-white">Seleccionar</span>
                                </div>
                            @endforeach
                        </div>
                    @elseif(request('search'))
                        <p class="mt-4 text-sm text-red-500 text-center">No se encontraron resultados.</p>
                    @endif
                </div>

                {{-- PASO 2: FORMULARIO DE ENVÍO --}}
                <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    
                    <input type="hidden" name="user_id" id="user_id_input" required>

                    {{-- Panel de Usuario Seleccionado --}}
                    <div id="panel_seleccionado" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-green-600 uppercase">Destinatario Confirmado</p>
                            <p class="text-lg font-black text-green-900 uppercase" id="nombre_seleccionado">--</p>
                            <p class="text-sm text-green-700 font-mono font-bold" id="dni_seleccionado">--</p>
                        </div>
                        <button type="button" onclick="resetearSeleccion()" class="text-red-500 text-xs font-bold underline hover:text-red-700">Cambiar</button>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">2. Asunto</label>
                            <input type="text" name="asunto" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500" placeholder="Ej: Resolución Ejecutiva N° 123-2026">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">3. Mensaje</label>
                            <textarea name="mensaje" rows="4" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500" placeholder="Escriba aquí el detalle..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">4. Adjuntar Archivo (PDF)</label>
                            <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors" id="dropzone">
                                <input type="file" name="archivo" id="archivo_input" accept="application/pdf" required 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       onchange="mostrarArchivo(this)">
                                
                                <div id="contenido_inicial">
                                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-sm font-bold text-gray-600">Haga clic o arrastre el PDF aquí</p>
                                    <p class="text-xs text-gray-400 mt-1">Máximo 10MB</p>
                                </div>

                                <div id="contenido_archivo" class="hidden">
                                    <div class="flex items-center justify-center gap-3 text-blue-600">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <div class="text-left">
                                            <p class="text-sm font-black" id="nombre_archivo">archivo.pdf</p>
                                            <p class="text-xs text-blue-400">Listo para subir</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-red-500 mt-2 font-bold hover:underline z-20 relative cursor-pointer" onclick="quitarArchivo(event)">Quitar archivo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-transform hover:-translate-y-1">
                            ENVIAR NOTIFICACIÓN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS ACTUALIZADOS --}}
    <script>
        // La función ahora recibe 4 parámetros para saber si es DNI o RUC
        function seleccionarUsuario(id, nombre, documento, tipoDoc) {
            document.getElementById('user_id_input').value = id;
            document.getElementById('nombre_seleccionado').innerText = nombre;
            // Aquí muestra dinámicamente "DNI: 123" o "RUC: 123"
            document.getElementById('dni_seleccionado').innerText = tipoDoc + ': ' + documento;
            
            document.getElementById('panel_seleccionado').classList.remove('hidden');
            document.getElementById('panel_seleccionado').scrollIntoView({behavior: 'smooth'});
        }

        function resetearSeleccion() {
            document.getElementById('user_id_input').value = '';
            document.getElementById('panel_seleccionado').classList.add('hidden');
        }

        function mostrarArchivo(input) {
            const archivo = input.files[0];
            if (archivo) {
                document.getElementById('contenido_inicial').classList.add('hidden');
                document.getElementById('contenido_archivo').classList.remove('hidden');
                document.getElementById('nombre_archivo').innerText = archivo.name;
                
                document.getElementById('dropzone').classList.add('bg-blue-50', 'border-blue-300');
                document.getElementById('dropzone').classList.remove('border-gray-300');
            }
        }

        function quitarArchivo(event) {
            event.preventDefault();
            const input = document.getElementById('archivo_input');
            input.value = '';

            document.getElementById('contenido_inicial').classList.remove('hidden');
            document.getElementById('contenido_archivo').classList.add('hidden');
            
            document.getElementById('dropzone').classList.remove('bg-blue-50', 'border-blue-300');
            document.getElementById('dropzone').classList.add('border-gray-300');
        }
    </script>
</x-app-layout>