<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center">
            <span class="w-2 h-8 bg-gorepa-500 rounded-full mr-3 shadow-[0_0_15px_rgba(87,193,199,0.4)]"></span>
            {{ __('Peticiones de Acceso') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-slate-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <p class="text-gray-500 font-medium">Gestiona las solicitudes de ciudadanos que requieren un usuario y clave para la Casilla Electrónica.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl shadow-sm animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif

            {{-- TABLA DE PETICIONES REALES --}}
            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-xl shadow-gorepa-500/5 sm:rounded-[2.5rem] border border-gray-100">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-white/50">
                    <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Solicitudes Pendientes</h3>
                    <span class="px-4 py-1 bg-gorepa-100 text-gorepa-700 rounded-full text-xs font-bold">
                        {{ $solicitudes->count() }} Pendientes
                    </span>
                </div>

                <div class="p-8 overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                                <th class="px-6 py-3">Fecha Solicitud</th>
                                <th class="px-6 py-3">Ciudadano / DNI</th>
                                <th class="px-6 py-3">Contacto</th>
                                <th class="px-6 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium">
                            @forelse($solicitudes as $s)
                                <tr class="bg-gray-50/50 hover:bg-white hover:shadow-md transition-all duration-300 group">
                                    <td class="px-6 py-4 rounded-l-2xl text-gray-500">
                                        {{ $s->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-gray-800 font-bold group-hover:text-gorepa-600 transition-colors">{{ $s->name }} {{ $s->apellido_paterno }}</span>
                                            <span class="text-xs text-gray-400">DNI: {{ $s->dni }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        <span class="block">{{ $s->email }}</span>
                                        @if($s->distrito)
                                            <span class="text-[10px] text-gorepa-600 font-bold uppercase">{{ $s->distrito }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 rounded-r-2xl text-center">
                                        <div class="flex justify-center space-x-2">
                                            {{-- Formulario para aprobar --}}
                                            <form action="{{ route('admin.aprobar', $s->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-6 py-2 bg-gorepa-500 hover:bg-gorepa-600 text-white rounded-xl text-xs font-bold transition-all transform hover:scale-105 active:scale-95 shadow-lg shadow-gorepa-500/20">
                                                    Aprobar
                                                </button>
                                            </form>

                                            {{-- Botón para rechazar (opcional - requiere ruta) --}}
                                            <button class="px-4 py-2 border border-gray-200 hover:bg-red-50 hover:text-red-600 text-gray-400 rounded-xl text-xs font-bold transition-all">
                                                Rechazar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                                        No hay peticiones de acceso pendientes en este momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MENSAJE DE AYUDA --}}
            <div class="mt-8 p-6 bg-gorepa-50 rounded-[2rem] border border-gorepa-100 flex items-start space-x-4">
                <div class="p-2 bg-white rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-gorepa-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-sm text-gorepa-800">
                    <p class="font-black uppercase tracking-tight mb-1">Nota del Sistema:</p>
                    <p class="font-medium opacity-80">Al aprobar una solicitud, el estado del usuario cambiará a <strong class="text-gorepa-600">Activo</strong>. Asegúrese de haber verificado los datos del DNI antes de proceder.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>