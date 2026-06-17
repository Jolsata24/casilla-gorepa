<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- ENCABEZADO --}}
            <div class="mb-8 text-center flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-800 tracking-tight">Redactar Notificación</h2>
                    <p class="text-gray-500 font-medium">Seleccione al destinatario y adjunte el documento oficial.</p>
                </div>
                {{-- NUEVO BOTÓN: ENVÍO MASIVO --}}
                <button type="button" onclick="abrirModalMasivo()" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-md flex items-center gap-2 transition-transform hover:-translate-y-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Envío Masivo
                </button>
            </div>

            {{-- MENSAJES DE ÉXITO Y ERROR --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm font-bold">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                {{-- PASO 1: BUSCADOR INDIVIDUAL --}}
                <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                    <form method="GET" action="{{ route('admin.crear') }}">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">1. Buscar Destinatario Individual</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar por DNI, RUC o Email...">
                            <button type="submit" class="px-6 py-3 bg-gray-800 text-white font-bold rounded-xl hover:bg-black transition-colors">BUSCAR</button>
                        </div>
                    </form>

                    {{-- Resultados de Búsqueda Individual --}}
                    @if(isset($usuarios) && $usuarios->count() > 0)
                        <div class="mt-4 grid gap-2">
                            <p class="text-xs text-gray-500 font-bold uppercase">Resultados encontrados:</p>
                            @foreach($usuarios as $u)
                                @php
                                    $esRUC = $u->tipo_documento === 'RUC';
                                    $nombreMostrar = $esRUC ? $u->razon_social : trim($u->name . ' ' . $u->apellido_paterno);
                                    $docMostrar = $esRUC ? $u->ruc : $u->dni;
                                    $tipoDoc = $esRUC ? 'RUC' : 'DNI';
                                @endphp
                                <div onclick="seleccionarUsuarioIndividual('{{ $u->id }}', '{{ addslashes($nombreMostrar) }}', '{{ $docMostrar }}', '{{ $tipoDoc }}')" 
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
                <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="p-8" id="formNotificacion">
                    @csrf
                    
                    {{-- CONTENEDOR DE USUARIOS SELECCIONADOS (ARREGLO DE IDs) --}}
                    <div id="contenedor_user_ids">
                        </div>
                    
                    <input type="hidden" name="archivo_firmado_base64" id="archivoFirmadoBase64">

                    {{-- Panel Visual de Selección --}}
                    <div id="panel_seleccionado" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-green-600 uppercase" id="titulo_seleccion">Destinatario Confirmado</p>
                            <p class="text-lg font-black text-green-900 uppercase" id="nombre_seleccionado">--</p>
                            <p class="text-sm text-green-700 font-mono font-bold" id="dni_seleccionado">--</p>
                        </div>
                        <button type="button" onclick="resetearSeleccion()" class="text-red-500 text-xs font-bold underline hover:text-red-700">Descartar</button>
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
                                </div>
                                <div id="contenido_archivo" class="hidden">
                                    <p class="text-sm font-black text-blue-600" id="nombre_archivo">archivo.pdf</p>
                                    <p class="text-xs text-red-500 mt-2 font-bold hover:underline cursor-pointer z-20 relative" onclick="quitarArchivo(event)">Quitar archivo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                        <button type="submit" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-xl transition-colors">Enviar sin firmar</button>
                        <button type="button" id="btnFirmarDNIe" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Firmar con DNIe y Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL MASIVO --}}
    <div id="modalMasivo" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative w-11/12 md:w-3/4 max-w-4xl bg-white rounded-2xl shadow-2xl p-6">
            <div class="flex justify-between items-center mb-4 border-b pb-4">
                <h3 class="text-xl font-black text-gray-800">Seleccionar Destinatarios (Masivo)</h3>
                <button onclick="cerrarModalMasivo()" class="text-gray-400 hover:text-red-500 font-bold text-2xl">&times;</button>
            </div>
            
            <div class="mb-4 bg-purple-50 p-3 rounded-lg flex justify-between items-center border border-purple-200">
                <label class="flex items-center font-bold text-purple-900 cursor-pointer">
                    <input type="checkbox" id="checkTodos" class="form-checkbox h-5 w-5 text-purple-600 rounded mr-3" checked onchange="toggleTodosUsuarios(this)">
                    Seleccionar / Desmarcar Todos
                </label>
                <span class="text-sm text-purple-700 font-medium">Total: {{ isset($todosLosUsuarios) ? count($todosLosUsuarios) : 0 }}</span>
            </div>

            {{-- Lista de Usuarios --}}
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-2 grid grid-cols-1 md:grid-cols-2 gap-2 bg-gray-50">
                @if(isset($todosLosUsuarios))
                    @foreach($todosLosUsuarios as $u)
                        @php
                            $nombreModal = $u->tipo_documento === 'RUC' ? $u->razon_social : trim($u->name . ' ' . $u->apellido_paterno);
                            $docModal = $u->tipo_documento === 'RUC' ? 'RUC: '.$u->ruc : 'DNI: '.$u->dni;
                        @endphp
                        <label class="flex items-start p-3 bg-white border rounded shadow-sm hover:bg-purple-50 cursor-pointer">
                            <input type="checkbox" value="{{ $u->id }}" class="cb-usuario form-checkbox h-5 w-5 text-purple-600 rounded mt-0.5 mr-3" checked>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800 leading-tight uppercase">{{ $nombreModal }}</p>
                                <p class="text-xs text-gray-500">{{ $docModal }}</p>
                            </div>
                        </label>
                    @endforeach
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button onclick="cerrarModalMasivo()" class="px-5 py-2 text-gray-600 font-bold hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button onclick="confirmarSeleccionMasiva()" class="px-5 py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow">Confirmar Selección</button>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // --- Lógica de Selección Individual ---
        function seleccionarUsuarioIndividual(id, nombre, documento, tipoDoc) {
            let container = document.getElementById('contenedor_user_ids');
            container.innerHTML = `<input type="hidden" name="user_ids[]" value="${id}">`;
            
            document.getElementById('titulo_seleccion').innerText = "Destinatario Individual";
            document.getElementById('nombre_seleccionado').innerText = nombre;
            document.getElementById('dni_seleccionado').innerText = tipoDoc + ': ' + documento;
            
            mostrarPanel();
        }

        // --- Lógica de Selección Masiva (Modal) ---
        function abrirModalMasivo() { document.getElementById('modalMasivo').classList.remove('hidden'); }
        function cerrarModalMasivo() { document.getElementById('modalMasivo').classList.add('hidden'); }

        function toggleTodosUsuarios(checkboxMaster) {
            let checkboxes = document.querySelectorAll('.cb-usuario');
            checkboxes.forEach(cb => cb.checked = checkboxMaster.checked);
        }

        function confirmarSeleccionMasiva() {
            let checkboxes = document.querySelectorAll('.cb-usuario:checked');
            if (checkboxes.length === 0) {
                alert("Debes seleccionar al menos un destinatario.");
                return;
            }

            // Llenar el formulario con los IDs
            let container = document.getElementById('contenedor_user_ids');
            container.innerHTML = ''; // Limpiar anteriores
            
            checkboxes.forEach(cb => {
                container.innerHTML += `<input type="hidden" name="user_ids[]" value="${cb.value}">`;
            });

            // Actualizar vista
            document.getElementById('titulo_seleccion').innerText = "Envío Múltiple Activo";
            document.getElementById('nombre_seleccionado').innerText = checkboxes.length + " DESTINATARIOS SELECCIONADOS";
            document.getElementById('dni_seleccionado').innerText = "Varios documentos";
            
            cerrarModalMasivo();
            mostrarPanel();
        }

        // --- Utilidades ---
        function mostrarPanel() {
            document.getElementById('panel_seleccionado').classList.remove('hidden');
            document.getElementById('panel_seleccionado').scrollIntoView({behavior: 'smooth'});
        }

        function resetearSeleccion() {
            document.getElementById('contenedor_user_ids').innerHTML = '';
            document.getElementById('panel_seleccionado').classList.add('hidden');
        }

        function mostrarArchivo(input) {
            if (input.files[0]) {
                document.getElementById('contenido_inicial').classList.add('hidden');
                document.getElementById('contenido_archivo').classList.remove('hidden');
                document.getElementById('nombre_archivo').innerText = input.files[0].name;
            }
        }

        function quitarArchivo(event) {
            event.preventDefault();
            document.getElementById('archivo_input').value = '';
            document.getElementById('contenido_inicial').classList.remove('hidden');
            document.getElementById('contenido_archivo').classList.add('hidden');
        }

        // --- LÓGICA DE FIRMA CON DNIE ---
        document.getElementById('btnFirmarDNIe').addEventListener('click', async function() {
            // Verificar si hay al menos un destinatario (input hidden renderizado)
            if (document.querySelectorAll('input[name="user_ids[]"]').length === 0) {
                alert("Por favor, selecciona al menos un destinatario o haz un envío masivo primero.");
                return;
            }

            const fileInput = document.getElementById('archivo_input');
            if (fileInput.files.length === 0) {
                alert("Por favor, selecciona un documento PDF primero.");
                return;
            }

            const file = fileInput.files[0];
            
            try {
                const base64PDF = await convertirABase64(file);
                const base64Limpio = base64PDF.split(',')[1]; 

                alert("Iniciando firma digital. Por favor, asegúrate de tener tu DNIe conectado e ingresa tu PIN cuando se solicite.");
                
                const btn = document.getElementById('btnFirmarDNIe');
                btn.innerHTML = 'Procesando firma...';
                btn.disabled = true;

                // --- FUNCIÓN A REEMPLAZAR POR LA OFICIAL DEL ESTADO ---
                const pdfFirmadoBase64 = await invocarReFirma(base64Limpio);

                document.getElementById('archivoFirmadoBase64').value = pdfFirmadoBase64;
                fileInput.removeAttribute('required');
                document.getElementById('formNotificacion').submit();

            } catch (error) {
                alert("Error al firmar: " + error);
                const btn = document.getElementById('btnFirmarDNIe');
                btn.innerHTML = 'Firmar con DNIe y Enviar';
                btn.disabled = false;
            }
        });

        function convertirABase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        }

        // Simulación ReFirma
        async function invocarReFirma(base64Original) {
            return new Promise((resolve, reject) => {
                // Aquí va el código de conexión local proporcionado por ReFirma
                reject("SDK de ReFirma no implementado aún. Añade la conexión oficial.");
            });
        }
    </script>
</x-app-layout>