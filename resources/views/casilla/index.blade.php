<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bandeja de Entrada - GOREPA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Mensajes de éxito o error --}}
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold mb-4">Mis Notificaciones Electrónicas</h3>

                    @if($notificaciones->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            <p>No tienes notificaciones pendientes.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Asunto / Documento
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Fecha de Envío
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notificaciones as $notificacion)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            @if($notificacion->esta_leido)
                                                <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                                    <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                                    <span class="relative">Leído</span>
                                                </span>
                                            @else
                                                <span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                                    <span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                                    <span class="relative">Sin Leer</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap font-bold">{{ $notificacion->asunto }}</p>
                                            <p class="text-gray-600 whitespace-no-wrap text-xs">{{ Str::limit($notificacion->mensaje, 50) }}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">
                                                {{ $notificacion->created_at->format('d/m/Y h:i A') }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <a href="{{ route('casilla.descargar', $notificacion->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 font-bold hover:underline">
                                                📥 Ver Expediente
                                            </a>
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
</x-app-layout>