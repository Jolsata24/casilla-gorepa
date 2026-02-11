<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- CABECERA DE BIENVENIDA --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-800 tracking-tight uppercase">Mi Casilla Electrónica</h2>
                    <p class="text-gray-500 font-medium mt-1">Bienvenido, <span class="text-gorepa-600 font-bold">{{ Auth::user()->name }}</span>. Aquí están sus documentos oficiales.</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs text-gray-400 font-bold uppercase">DNI Registrado</p>
                        <p class="font-mono font-bold text-gray-800">{{ Auth::user()->dni }}</p>
                    </div>
                    <div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.56 2.032-2 2.032A2 2 0 0110 6z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- 1. SECCIÓN: DOCUMENTOS NUEVOS (PENDIENTES) --}}
            @if($nuevas->count() > 0)
                <div class="mb-12">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-wide">Pendientes de Lectura</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($nuevas as $noti)
                        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-red-500 relative overflow-hidden group hover:shadow-xl transition-all">
                            {{-- Fondo decorativo --}}
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>

                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="bg-red-100 text-red-600 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wider">Nuevo</span>
                                    <span class="text-xs text-gray-400 font-medium">{{ $noti->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <h4 class="text-xl font-bold text-gray-900 mb-2 leading-tight">{{ $noti->asunto }}</h4>
                                <p class="text-sm text-gray-500 mb-6 line-clamp-2">{{ $noti->mensaje }}</p>

                                <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                    <div class="flex items-center text-xs text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $noti->created_at->format('d M Y') }}
                                    </div>
                                    
                                    {{-- BOTÓN DE DESCARGA --}}
                                    <a href="{{ route('casilla.descargar', $noti->id) }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-md transition-colors gap-2">
                                        <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Leer Documento
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Mensaje si no hay nuevos --}}
                <div class="mb-12 bg-green-50/50 border border-green-100 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-green-800 font-bold text-lg">¡Estás al día!</h3>
                    <p class="text-green-600/80 text-sm">No tienes documentos pendientes de lectura en este momento.</p>
                </div>
            @endif


            {{-- 2. SECCIÓN: HISTORIAL (YA LEÍDOS) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Historial de Archivos
                    </h3>
                    <span class="text-xs font-semibold bg-gray-200 text-gray-600 px-2 py-1 rounded">{{ $historial->total() }} documentos</span>
                </div>

                @if($historial->count() > 0)
                    <div class="divide-y divide-gray-50">
                        @foreach($historial as $archivo)
                        <div class="p-6 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            {{-- Info del Documento --}}
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 pt-1">
                                    <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ $archivo->asunto }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($archivo->mensaje, 60) }}</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Recibido: {{ $archivo->created_at->format('d/m/Y') }}
                                        </span>
                                        <span class="text-[10px] text-green-600 flex items-center gap-1 bg-green-50 px-1.5 py-0.5 rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Leído: {{ \Carbon\Carbon::parse($archivo->fecha_lectura)->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Botón Descargar (Secundario) --}}
                            <div class="shrink-0">
                                <a href="{{ route('casilla.descargar', $archivo->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-200 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gorepa-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Volver a Descargar
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $historial->links() }}
                    </div>
                @else
                    <div class="p-10 text-center text-gray-400">
                        <p>No tienes documentos en el historial.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>