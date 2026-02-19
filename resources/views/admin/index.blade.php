<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bandeja de Salida - Gestión de Notificaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. TARJETAS DE RESUMEN (KPIs) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-bold uppercase">Total Enviados</div>
                            <div class="text-3xl font-black text-gray-800">{{ $enviadas }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-bold uppercase">Leídos</div>
                            <div class="text-3xl font-black text-gray-800">{{ $leidas }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-bold uppercase">Pendientes</div>
                            <div class="text-3xl font-black text-gray-800">{{ $pendientes }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TABLA DE NOTIFICACIONES --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Encabezado de la Sección --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700 mb-4 md:mb-0">
                            Historial de Notificaciones
                        </h3>
                        <a href="{{ route('admin.crear') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:-translate-y-0.5 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Nueva Notificación
                        </a>
                    </div>

                    @if($notificaciones->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span class="text-lg font-medium">No hay notificaciones enviadas aún.</span>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha Envío</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Destinatario</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Asunto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($notificaciones as $notificacion)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        
                                        {{-- FECHA ENVÍO --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="font-medium">{{ $notificacion->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $notificacion->created_at->format('h:i A') }}</div>
                                        </td>

                                        {{-- DESTINATARIO (DINÁMICO DNI/RUC) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $esRUC = $notificacion->user->tipo_documento === 'RUC';
                                                $nombreDestinatario = $esRUC ? $notificacion->user->razon_social : trim($notificacion->user->name . ' ' . $notificacion->user->apellido_paterno);
                                                $tipoDoc = $esRUC ? 'RUC' : 'DNI';
                                                $numDoc = $esRUC ? $notificacion->user->ruc : $notificacion->user->dni;
                                                $inicial = substr($nombreDestinatario, 0, 1);
                                            @endphp
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-3 uppercase">
                                                    {{ $inicial }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900" title="{{ $nombreDestinatario }}">{{ Str::limit($nombreDestinatario, 30) }}</div>
                                                    <div class="text-xs text-gray-500 font-mono">{{ $tipoDoc }}: {{ $numDoc }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- ASUNTO --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 font-medium">{{ $notificacion->asunto }}</div>
                                            <div class="text-xs text-gray-500 truncate max-w-xs" title="{{ $notificacion->mensaje }}">
                                                {{ Str::limit($notificacion->mensaje, 50) }}
                                            </div>
                                        </td>
                                        
                                        {{-- ESTADO DE LECTURA (Con Hora Legal) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($notificacion->fecha_lectura)
                                                <div class="flex flex-col">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit mb-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        LEÍDO
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($notificacion->fecha_lectura)->format('d/m/Y') }}
                                                    </span>
                                                    <span class="text-xs font-bold text-gray-600 font-mono">
                                                        {{ \Carbon\Carbon::parse($notificacion->fecha_lectura)->format('H:i:s') }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    PENDIENTE
                                                </span>
                                            @endif
                                        </td>

                                        {{-- ACCIONES (DESCARGAR ORIGINAL Y CARGO) --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex flex-col space-y-2 items-end">
                                                
                                                {{-- 1. Ver Documento Original --}}
                                                <a href="{{ route('documento.seguro', $notificacion->id) }}" class="text-blue-600 hover:text-blue-900 flex items-center hover:underline" target="_blank" title="Ver documento original enviado">
                                                    <span>Original</span>
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>

                                                {{-- 2. Descargar Cargo de Recepción (Solo si ya se leyó) --}}
                                                @if($notificacion->fecha_lectura)
                                                    <a href="{{ route('admin.cargo', $notificacion->id) }}" class="text-green-700 hover:text-green-900 flex items-center hover:underline font-bold" title="Descargar Constancia Legal de Lectura">
                                                        <span>Descargar Cargo</span>
                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </a>
                                                @else
                                                    <span class="text-gray-300 text-xs italic flex items-center cursor-not-allowed">
                                                        Sin cargo
                                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Paginación --}}
                        <div class="mt-4">
                            {{ $notificaciones->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>