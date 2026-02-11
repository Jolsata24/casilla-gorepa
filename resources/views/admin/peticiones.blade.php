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
                        <p class="text-sm text-gray-500">Revise la identidad del ciudadano antes de aprobar y generar sus credenciales.</p>
                    </div>

                    @if($solicitudes->isEmpty())
                        <div class="text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-gray-600">Todo al día</h3>
                            <p class="text-gray-400">No hay solicitudes pendientes de aprobación.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fecha Solicitud</th>
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Documento (DNI)</th>
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Datos Personales</th>
                                        <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Acción Requerida</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($solicitudes as $solicitud)
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="p-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $solicitud->created_at->format('d/m/Y H:i') }}
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
                                        <td class="p-4 text-right">
                                            <form action="{{ route('admin.aprobar.pdf', $solicitud->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-md hover:shadow-lg transition-all transform active:scale-95">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Aprobar y PDF
                                                </button>
                                            </form>
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