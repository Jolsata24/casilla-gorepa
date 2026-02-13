<x-app-layout>
    <x-slot name="header">
        {{ __('Mi Casilla Electrónica') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6 h-[calc(100vh-140px)]">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl h-full flex border border-gray-200">
            
            {{-- SIDEBAR IZQUIERDO (Menú de Carpetas) --}}
            <div class="w-64 bg-gray-50 border-r border-gray-200 flex flex-col p-4">
                
                {{-- Botón Redactar (Decorativo en cliente, funcional si quisieras responder) --}}
                <div class="mb-6">
                    <button onclick="document.getElementById('modal-crear-etiqueta').showModal()" class="flex items-center justify-center gap-2 w-full py-3 bg-white hover:shadow-md border border-gray-200 rounded-xl text-gray-700 font-bold transition-all hover:bg-gray-50">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span>Nueva Carpeta</span>
                    </button>
                </div>

                <nav class="space-y-1 flex-1 overflow-y-auto">
                    {{-- Bandejas del Sistema --}}
                    <a href="{{ route('casilla.index', ['folder' => 'inbox']) }}" class="flex items-center px-4 py-2.5 rounded-lg {{ $filtro == 'inbox' ? 'bg-blue-100 text-blue-800 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ $filtro == 'inbox' ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        Recibidos
                    </a>

                    <a href="{{ route('casilla.index', ['folder' => 'starred']) }}" class="flex items-center px-4 py-2.5 rounded-lg {{ $filtro == 'starred' ? 'bg-blue-100 text-blue-800 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ $filtro == 'starred' ? 'text-yellow-500 fill-current' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.519 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.519-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        Importante
                    </a>

                    <div class="mt-4 mb-2 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Mis Carpetas</div>

                    {{-- Carpetas del Usuario --}}
                    @foreach($etiquetas as $etiqueta)
                        <a href="{{ route('casilla.index', ['folder' => $etiqueta->id]) }}" class="flex items-center justify-between px-4 py-2 rounded-lg {{ $filtro == $etiqueta->id ? 'bg-blue-100 text-blue-800 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                {{ $etiqueta->nombre }}
                            </div>
                        </a>
                    @endforeach

                </nav>
            </div>

            {{-- LISTA DE CORREOS (Derecha) --}}
            <div class="flex-1 flex flex-col bg-white">
                
                {{-- Barra superior de la lista (Herramientas) --}}
                <div class="h-14 border-b border-gray-100 flex items-center justify-between px-6 bg-white sticky top-0 z-10">
                    <div class="text-sm font-bold text-gray-500">
                        @if($filtro == 'inbox') Bandeja de Entrada @elseif($filtro == 'starred') Importantes @else Carpeta Personal @endif
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $mensajes->total() }} documentos
                    </div>
                </div>

                {{-- Lista --}}
                <div class="flex-1 overflow-y-auto">
                    @if($mensajes->isEmpty())
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 opacity-60">
                            <svg class="w-20 h-20 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>No hay documentos aquí</p>
                        </div>
                    @else
                        <ul>
                            @foreach($mensajes as $msg)
                                <li class="group flex items-center px-4 py-3 border-b border-gray-100 hover:shadow-sm hover:bg-gray-50 transition-all cursor-pointer relative">
                                    
                                    {{-- 1. Checkbox y Estrella --}}
                                    <div class="flex items-center gap-3 mr-4">
                                        <form action="{{ route('casilla.destacar', $msg->id) }}" method="POST">
                                            @csrf
                                            <button class="focus:outline-none">
                                                <svg class="w-5 h-5 {{ $msg->es_destacado ? 'text-yellow-400 fill-current' : 'text-gray-300 hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.519 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.519-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- 2. Contenido Principal --}}
                                    <div class="flex-1 min-w-0" onclick="window.location='{{ route('documento.seguro', $msg->id) }}'">
                                        <div class="flex justify-between mb-1">
                                            <span class="font-bold text-gray-800 text-sm truncate {{ !$msg->fecha_lectura ? 'font-black' : 'font-medium' }}">
                                                {{ $msg->asunto }}
                                                @if(!$msg->fecha_lectura) <span class="bg-blue-100 text-blue-800 text-[10px] px-2 py-0.5 rounded-full ml-2">NUEVO</span> @endif
                                            </span>
                                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $msg->created_at->format('d M H:i') }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">{{ $msg->mensaje }}</p>
                                    </div>

                                    {{-- 3. Acciones Flotantes (Mover a) --}}
                                    <div class="ml-4 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-2">
                                        {{-- Dropdown para mover (Simple con HTML nativo por ahora) --}}
                                        <form action="{{ route('casilla.mover', $msg->id) }}" method="POST" class="relative">
                                            @csrf
                                            <select name="etiqueta_id" onchange="this.form.submit()" class="text-xs border-gray-200 rounded-md focus:ring-0 focus:border-blue-300 py-1 pl-2 pr-6 bg-gray-50 hover:bg-white cursor-pointer">
                                                <option value="" disabled selected>Mover a...</option>
                                                <option value="inbox">📥 Recibidos</option>
                                                @foreach($etiquetas as $et)
                                                    <option value="{{ $et->id }}">📁 {{ $et->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL NATIVO (DIALOG) PARA CREAR CARPETA --}}
    <dialog id="modal-crear-etiqueta" class="p-6 rounded-2xl shadow-2xl border-0 backdrop:bg-black/30">
        <form action="{{ route('casilla.etiqueta.store') }}" method="POST">
            @csrf
            <h3 class="font-bold text-lg mb-4 text-gray-800">Nueva Carpeta Personal</h3>
            <input type="text" name="nombre" placeholder="Nombre (ej. Contratos 2026)" class="w-full border-gray-300 rounded-xl mb-4 focus:ring-blue-500 focus:border-blue-500" required maxlength="20">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-crear-etiqueta').close()" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">Crear</button>
            </div>
        </form>
    </dialog>

</x-app-layout>