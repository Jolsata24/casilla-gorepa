<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight">
                Directorio de Usuarios
            </h2>
            <span class="px-3 py-1 bg-blue-100 rounded-full text-xs font-bold text-blue-600">
                TOTAL: {{ $usuarios->total() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Listado General</h3>
                        <p class="text-sm text-gray-500">Visualice la información de todos los usuarios registrados en el sistema.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Registro</th>
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Documento</th>
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Usuario / Empresa</th>
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Estado</th>
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($usuarios as $user)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    
                                    <td class="p-4 whitespace-nowrap">
                                        <span class="bg-gray-100 text-gray-700 font-mono font-bold px-2 py-1 rounded text-sm">
                                            {{ $user->tipo_documento == 'RUC' ? $user->ruc : $user->dni }}
                                        </span>
                                        <span class="text-[10px] ml-1 text-gray-400 font-bold uppercase">{{ $user->tipo_documento }}</span>
                                    </td>
                                    
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-800 uppercase text-sm">
                                                {{ $user->tipo_documento == 'RUC' ? $user->razon_social : ($user->name . ' ' . $user->apellido_paterno) }}
                                            </span>
                                            <span class="text-xs text-blue-500">{{ $user->email }}</span>
                                        </div>
                                    </td>

                                    <td class="p-4 text-center">
                                        @if($user->is_admin)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">ADMIN</span>
                                        @elseif($user->status == 1)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">ACTIVO</span>
                                        @else
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">PENDIENTE</span>
                                        @endif
                                    </td>
                                    
                                    <td class="p-4 text-right">
                                        <button onclick="verDetalles(this)" 
                                                data-tipo="{{ $user->tipo_documento }}"
                                                data-dni="{{ $user->dni }}"
                                                data-ruc="{{ $user->ruc }}"
                                                data-razon="{{ $user->razon_social }}"
                                                data-nombres="{{ $user->name }}"
                                                data-paterno="{{ $user->apellido_paterno }}"
                                                data-materno="{{ $user->apellido_materno }}"
                                                data-email="{{ $user->email }}"
                                                data-celular="{{ $user->celular }}"
                                                data-ubicacion="{{ $user->departamento }} / {{ $user->provincia }} / {{ $user->distrito }}"
                                                data-direccion="{{ $user->direccion }}"
                                                data-documento="{{ $user->documento_confianza ? asset('storage/' . $user->documento_confianza) : '' }}"
                                                class="inline-flex items-center justify-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-bold uppercase rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Revisar
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETALLE (Es el mismo código que en peticiones) --}}
    <div id="modalDetalle" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full max-w-lg">
                
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Detalle del Usuario</h3>
                    <button type="button" onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-blue-100 text-blue-800 font-black text-2xl px-6 py-2 rounded-lg tracking-widest border border-blue-200" id="m_dni_ruc"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 p-3 rounded-lg" id="caja_nombres">
                            <p class="text-xs text-gray-400 uppercase font-bold" id="lbl_nombres">Nombres</p>
                            <p class="font-bold text-gray-800 mt-1 uppercase" id="m_nombres"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg" id="caja_apellidos">
                            <p class="text-xs text-gray-400 uppercase font-bold">Apellidos</p>
                            <p class="font-bold text-gray-800 mt-1 uppercase" id="m_apellidos"></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg hidden" id="caja_representante">
                        <p class="text-xs text-gray-400 uppercase font-bold">Representante Legal / Contacto</p>
                        <p class="font-bold text-gray-800 mt-1 uppercase" id="m_representante"></p>
                    </div>

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

                    <div class="border-t border-gray-100 pt-3 bg-blue-50 -mx-6 px-6 pb-4 mt-2">
                        <p class="text-xs text-blue-400 uppercase font-bold mt-3">Ubicación Registrada</p>
                        <p class="text-sm font-bold text-gray-800 mt-1 flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span id="m_ubicacion"></span>
                        </p>
                        <p class="text-xs text-gray-500 italic mt-1 pl-6" id="m_direccion"></p>
                    </div>

                    <div class="border-t border-gray-100 pt-3 mt-4" id="caja_documento">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-2">Documento de Acreditación</p>
                        <a id="btn_ver_documento" href="#" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-bold rounded-lg transition-colors border border-indigo-200 w-full justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path></svg>
                            Ver Documento
                        </a>
                        <p id="txt_sin_documento" class="text-sm text-gray-500 italic hidden text-center py-2 bg-gray-50 rounded-lg">No se adjuntó documento.</p>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" onclick="cerrarModal()" class="w-full inline-flex justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verDetalles(btn) {
            const d = btn.dataset;
            const esRUC = d.tipo === 'RUC';

            document.getElementById('m_dni_ruc').innerText = esRUC ? `RUC: ${d.ruc}` : `DNI: ${d.dni}`;

            if(esRUC) {
                document.getElementById('lbl_nombres').innerText = 'Razón Social';
                document.getElementById('m_nombres').innerText = d.razon;
                document.getElementById('caja_apellidos').classList.add('hidden');
                document.getElementById('caja_nombres').classList.add('col-span-2');
                document.getElementById('caja_representante').classList.remove('hidden');
                document.getElementById('m_representante').innerText = d.nombres;
            } else {
                document.getElementById('lbl_nombres').innerText = 'Nombres';
                document.getElementById('m_nombres').innerText = d.nombres;
                document.getElementById('m_apellidos').innerText = `${d.paterno} ${d.materno}`;
                document.getElementById('caja_apellidos').classList.remove('hidden');
                document.getElementById('caja_nombres').classList.remove('col-span-2');
                document.getElementById('caja_representante').classList.add('hidden');
            }

            document.getElementById('m_email').innerText = d.email;
            document.getElementById('m_celular').innerText = d.celular || 'No registrado';
            
            let ubicacion = d.ubicacion;
            if(!ubicacion || ubicacion.trim() === '/  /') {
                ubicacion = 'Sin información de ubicación';
                document.getElementById('m_direccion').innerText = '';
            } else {
                document.getElementById('m_direccion').innerText = d.direccion || '';
            }
            document.getElementById('m_ubicacion').innerText = ubicacion;

            const btnVerDoc = document.getElementById('btn_ver_documento');
            const txtSinDoc = document.getElementById('txt_sin_documento');

            if(d.documento && d.documento.trim() !== '') {
                btnVerDoc.href = d.documento;
                btnVerDoc.classList.remove('hidden');
                btnVerDoc.classList.add('inline-flex');
                txtSinDoc.classList.add('hidden');
            } else {
                btnVerDoc.href = '#';
                btnVerDoc.classList.add('hidden');
                btnVerDoc.classList.remove('inline-flex');
                txtSinDoc.classList.remove('hidden');
            }

            document.getElementById('modalDetalle').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('modalDetalle').classList.add('hidden');
        }
    </script>
</x-app-layout>