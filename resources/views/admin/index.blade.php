<x-app-layout>
    <div class="py-10 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- CABECERA Y ACCIÓN PRINCIPAL --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-3xl font-black text-gray-800 tracking-tight">Bandeja de Salida</h2>
                    <p class="text-gray-500 font-medium mt-1">Historial de documentos y notificaciones enviadas.</p>
                </div>
                <a href="{{ route('admin.crear') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 hover:bg-black text-white text-sm font-bold uppercase tracking-wide rounded-xl shadow-lg transform transition hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Redactar Nueva
                </a>
            </div>

            {{-- TARJETAS DE RESUMEN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Enviados</p>
                        <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $enviadas }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Confirmados (Leídos)</p>
                        <h3 class="text-3xl font-black text-green-600 mt-1">{{ $leidas }}</h3>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pendientes de Lectura</p>
                        <h3 class="text-3xl font-black text-orange-500 mt-1">{{ $pendientes }}</h3>
                    </div>
                    <div class="p-3 bg-orange-50 text-orange-500 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- TABLA DE NOTIFICACIONES --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($notificaciones->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
    <thead>
        <tr>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asunto</th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
            {{-- 1. AGREGA ESTE ENCABEZADO --}}
            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Lectura</th> 
            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach($notificaciones as $notificacion)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ $notificacion->created_at->format('d/m/Y H:i') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ $notificacion->user->name }} {{ $notificacion->user->apellido_paterno }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ $notificacion->asunto }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                @if($notificacion->ruta_archivo_pdf)
                    <a href="{{ route('documento.seguro', $notificacion->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-bold underline">
                        Ver PDF
                    </a>
                @else
                    <span class="text-red-500 text-xs">--</span>
                @endif
            </td>
            
            {{-- 2. AGREGA ESTA CELDA PARA VER LA IP --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                @if($notificacion->ip_lectura)
                    {{ $notificacion->ip_lectura }}
                @else
                    <span class="text-gray-300">--</span>
                @endif
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($notificacion->fecha_lectura)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Leído
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Pendiente
                    </span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
                        
                        {{-- Paginación bonita --}}
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $notificaciones->links() }} 
                        </div>
                    </div>
                @else
                    <div class="text-center py-20">
                        <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h3 class="text-gray-800 font-bold">Bandeja Vacía</h3>
                        <p class="text-gray-500 text-sm">Aún no se han enviado notificaciones.</p>
                        <a href="{{ route('admin.crear') }}" class="mt-4 inline-block text-gorepa-600 font-bold text-sm hover:underline">Empezar a redactar</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>