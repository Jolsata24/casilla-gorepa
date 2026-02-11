<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- ENCABEZADO --}}
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">Redactar Notificación</h2>
                <p class="text-gray-500 font-medium">Seleccione al ciudadano y adjunte el documento oficial.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                {{-- PASO 1: BUSCADOR DE USUARIO --}}
                <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                    <form method="GET" action="{{ route('admin.crear') }}" class="relative">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">1. Buscar Destinatario</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" name="search" value="{{ $search }}" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gorepa-500 focus:border-gorepa-500 transition-all text-sm font-medium" placeholder="Buscar por DNI, Nombre o Email...">
                            </div>
                            <button type="submit" class="px-6 py-3 bg-gray-800 text-white font-bold rounded-xl text-sm hover:bg-black transition-colors">
                                BUSCAR
                            </button>
                        </div>
                    </form>

                    {{-- Resultados de Búsqueda --}}
                    @if($search && $usuarios->count() > 0)
                        <div class="mt-4 grid grid-cols-1 gap-3">
                            <p class="text-xs text-gray-500 font-bold uppercase">Resultados encontrados:</p>
                            @foreach($usuarios as $u)
                                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl hover:border-gorepa-400 cursor-pointer transition-all group"
                                     onclick="document.getElementById('user_id').value = '{{ $u->id }}'; document.getElementById('selected_user').innerText = '{{ $u->name }} ({{ $u->dni }})'; document.getElementById('selection_panel').classList.remove('hidden');">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gorepa-50 text-gorepa-600 flex items-center justify-center font-bold">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">{{ $u->name }} {{ $u->apellido_paterno }}</p>
                                            <p class="text-xs text-gray-500">{{ $u->dni }} &bull; {{ $u->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-gorepa-600 bg-gorepa-50 px-3 py-1 rounded-full group-hover:bg-gorepa-600 group-hover:text-white transition-colors">Seleccionar</span>
                                </div>
                            @endforeach
                        </div>
                    @elseif($search)
                        <p class="mt-4 text-sm text-red-500 font-medium text-center bg-red-50 p-3 rounded-xl">No se encontraron ciudadanos con ese criterio.</p>
                    @endif
                </div>

                {{-- PASO 2: FORMULARIO DE ENVÍO --}}
                <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" required>

                    {{-- Panel de Usuario Seleccionado --}}
                    <div id="selection_panel" class="hidden mb-8 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-blue-400 uppercase">Destinatario Seleccionado</p>
                            <p class="text-lg font-black text-blue-900" id="selected_user">--</p>
                        </div>
                        <div class="h-8 w-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Asunto --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">2. Asunto del Documento</label>
                            <input type="text" name="asunto" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-gorepa-500 focus:border-gorepa-500 text-sm font-medium transition-all" placeholder="Ej: Resolución Ejecutiva N° 123-2024">
                        </div>

                        {{-- Mensaje --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">3. Mensaje / Detalle</label>
                            <textarea name="mensaje" rows="4" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-gorepa-500 focus:border-gorepa-500 text-sm font-medium transition-all" placeholder="Escriba aquí el detalle de la notificación..."></textarea>
                        </div>

                        {{-- Archivo PDF --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">4. Adjuntar Archivo (PDF)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors relative cursor-pointer">
                                <input type="file" name="archivo" accept="application/pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="text-sm font-bold text-gray-600">Haga clic para subir el PDF</p>
                                <p class="text-xs text-gray-400 mt-1">Solo archivos PDF (Máx. 10MB)</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-gorepa-600 to-gorepa-500 hover:to-gorepa-600 text-white font-bold rounded-xl shadow-lg transform transition hover:-translate-y-0.5 text-sm uppercase tracking-wide">
                            Enviar Notificación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>