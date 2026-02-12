<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight">
                Gestión de Accesos
            </h2>
            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-500">
                PENDIENTES: {{ $solicitudes->count() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Mensaje de Éxito --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Solicitudes de Ciudadanos</h3>
                        <p class="text-sm text-gray-500">Revise la información detallada antes de aprobar y generar credenciales.</p>
                    </div>

                    @if($solicitudes->isEmpty())
                        <div class="text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-gray-600">Todo al día</h3>
                            <p class="text-gray-400">No hay solicitudes pendientes.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fecha</th>
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">DNI</th>
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Solicitante</th>
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($solicitudes as $solicitud)
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="p-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $solicitud->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="bg-gray-100 text-gray-700 font-mono font-bold px-2 py-1 rounded text-sm">
                                                {{ $solicitud->dni }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-800 capitalize">{{ $solicitud->name }} {{ $solicitud->apellido_paterno }}</span>
                                                <span class="text-xs text-blue-500">{{ $solicitud->email }}</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right flex justify-end gap-2">
                                            
                                            {{-- BOTÓN REVISAR: Carga los datos guardados en BD (Sin API) --}}
                                            <button onclick="verDetalles(this)" 
                                                    data-dni="{{ $solicitud->dni }}"
                                                    data-nombres="{{ $solicitud->name }}"
                                                    data-paterno="{{ $solicitud->apellido_paterno }}"
                                                    data-materno="{{ $solicitud->apellido_materno }}"
                                                    data-email="{{ $solicitud->email }}"
                                                    data-celular="{{ $solicitud->celular }}"
                                                    {{-- Concatenamos la ubicación --}}
                                                    data-ubicacion="{{ $solicitud->departamento }} / {{ $solicitud->provincia }} / {{ $solicitud->distrito }}"
                                                    data-direccion="{{ $solicitud->direccion }}"
                                                    class="inline-flex items-center justify-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-bold uppercase rounded-lg transition-colors"
                                                    title="Ver detalles completos">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Revisar
                                            </button>

                                            {{-- APROBAR --}}
                                            <form action="{{ route('admin.aprobar.pdf', $solicitud->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase rounded-lg shadow transition-transform active:scale-95" title="Aprobar y Generar Credenciales">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>

                                            {{-- DENEGAR --}}
                                            @if(Route::has('admin.denegar'))
                                            <form action="{{ route('admin.denegar', $solicitud->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de rechazar esta solicitud? Se eliminará el registro.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold uppercase rounded-lg transition-colors" title="Denegar solicitud">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETALLE (ÚNICO MODAL) --}}
    <div id="modalDetalle" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full max-w-lg">
                
                {{-- Cabecera --}}
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Detalle del Ciudadano</h3>
                    <button type="button" onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Cuerpo --}}
                <div class="p-6 space-y-4">
                    {{-- DNI Grande --}}
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-blue-100 text-blue-800 font-black text-2xl px-6 py-2 rounded-lg tracking-widest border border-blue-200" id="m_dni"></div>
                    </div>

                    {{-- Nombres --}}
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase font-bold">Nombres</p>
                            <p class="font-bold text-gray-800 mt-1" id="m_nombres"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase font-bold">Apellidos</p>
                            <p class="font-bold text-gray-800 mt-1" id="m_apellidos"></p>
                        </div>
                    </div>

                    {{-- Contacto --}}
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-2">Contacto</p>
                        <div class="flex flex-col gap-2">
                            <p class="text-sm text-gray-700 flex items-center gap-2">
                                <span class="bg-blue-50 p-1 rounded text-blue-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </span>
                                <span id="m_email" class="font-medium"></span>
                            </p>
                            <p class="text-sm text-gray-700 flex items-center gap-2">
                                <span class="bg-green-50 p-1 rounded text-green-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <span id="m_celular" class="font-medium"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Ubicación --}}
                    <div class="border-t border-gray-100 pt-3 bg-blue-50 -mx-6 px-6 pb-4 mt-2">
                        <p class="text-xs text-blue-400 uppercase font-bold mt-3">Ubicación Registrada</p>
                        <p class="text-sm font-bold text-gray-800 mt-1 flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span id="m_ubicacion"></span>
                        </p>
                        <p class="text-xs text-gray-500 italic mt-1 pl-6" id="m_direccion"></p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" onclick="cerrarModal()" class="w-full inline-flex justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT ÚNICO Y LIMPIO --}}
    <script>
        function verDetalles(btn) {
            // 1. Extraer todos los datos del dataset del botón
            const d = btn.dataset;

            // 2. Llenar los campos del Modal
            document.getElementById('m_dni').innerText = d.dni;
            document.getElementById('m_nombres').innerText = d.nombres;
            document.getElementById('m_apellidos').innerText = `${d.paterno} ${d.materno}`;
            document.getElementById('m_email').innerText = d.email;
            document.getElementById('m_celular').innerText = d.celular || 'No registrado';
            
            // 3. Lógica para Ubicación (Si vienen vacíos los campos)
            let ubicacion = d.ubicacion;
            // Si la cadena es solo " / / " significa que no hay datos
            if(!ubicacion || ubicacion.trim() === '/  /') {
                ubicacion = 'Sin información de ubicación';
                document.getElementById('m_direccion').innerText = '';
            } else {
                document.getElementById('m_direccion').innerText = d.direccion || '';
            }
            document.getElementById('m_ubicacion').innerText = ubicacion;

            // 4. Mostrar el Modal
            document.getElementById('modalDetalle').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('modalDetalle').classList.add('hidden');
        }
    </script>
</x-app-layout>