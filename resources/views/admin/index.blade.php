<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Control de Notificaciones Enviadas - GOREPA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Registro de Notificaciones</h3>
                    <a href="{{ route('admin.crear') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        + Nueva Notificación
                    </a>
                </div>

                <table class="min-w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">Ciudadano</th>
                            <th class="border px-4 py-2">Asunto</th>
                            <th class="border px-4 py-2">Enviado</th>
                            <th class="border px-4 py-2">Estado</th>
                            <th class="border px-4 py-2">Fecha Lectura</th>
                            <th class="border px-4 py-2">IP Lectura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notificaciones as $n)
                        <tr>
                            <td class="border px-4 py-2">{{ $n->user->name }}</td>
                            <td class="border px-4 py-2">{{ $n->asunto }}</td>
                            <td class="border px-4 py-2 text-sm">{{ $n->created_at->format('d/m/Y H:i') }}</td>
                            <td class="border px-4 py-2">
                                @if($n->fecha_lectura)
                                    <span class="text-green-600 font-bold">Leído</span>
                                @else
                                    <span class="text-red-500 italic">Pendiente</span>
                                @endif
                            </td>
                            <td class="border px-4 py-2 text-sm">{{ $n->fecha_lectura ?? '-' }}</td>
                            <td class="border px-4 py-2 text-xs font-mono">{{ $n->ip_lectura ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>