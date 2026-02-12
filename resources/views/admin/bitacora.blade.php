<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Auditoría del Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-bold mb-4">Registro de Actividad</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse block md:table">
                            <thead class="block md:table-header-group">
                                <tr class="border border-grey-500 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto  md:relative ">
                                    <th class="bg-gray-100 p-2 text-gray-600 font-bold md:border md:border-grey-500 text-left block md:table-cell">Fecha/Hora</th>
                                    <th class="bg-gray-100 p-2 text-gray-600 font-bold md:border md:border-grey-500 text-left block md:table-cell">Usuario</th>
                                    <th class="bg-gray-100 p-2 text-gray-600 font-bold md:border md:border-grey-500 text-left block md:table-cell">Acción</th>
                                    <th class="bg-gray-100 p-2 text-gray-600 font-bold md:border md:border-grey-500 text-left block md:table-cell">IP</th>
                                    <th class="bg-gray-100 p-2 text-gray-600 font-bold md:border md:border-grey-500 text-left block md:table-cell">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group">
                                @foreach($registros as $log)
                                    <tr class="bg-white border border-grey-500 md:border-none block md:table-row">
                                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                            <span class="inline-block w-1/3 md:hidden font-bold">Fecha</span>
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                            <span class="inline-block w-1/3 md:hidden font-bold">Usuario</span>
                                            @if($log->user)
                                                <span class="font-bold">{{ $log->user->name }}</span>
                                                <br><span class="text-xs text-gray-500">DNI: {{ $log->user->dni ?? 'N/A' }}</span>
                                            @else
                                                <span class="text-gray-400">Anónimo / Sistema</span>
                                            @endif
                                        </td>
                                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                            <span class="inline-block w-1/3 md:hidden font-bold">Acción</span>
                                            <span class="px-2 py-1 rounded text-xs text-white 
                                                {{ $log->accion == 'DESCARGA_PDF' ? 'bg-blue-500' : 
                                                  ($log->accion == 'LOGIN' ? 'bg-green-500' : 'bg-gray-500') }}">
                                                {{ $log->accion }}
                                            </span>
                                        </td>
                                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                            <span class="inline-block w-1/3 md:hidden font-bold">IP</span>
                                            {{ $log->ip_address }}
                                        </td>
                                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell text-sm">
                                            <span class="inline-block w-1/3 md:hidden font-bold">Detalles</span>
                                            {{ Str::limit($log->detalles, 50) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $registros->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>