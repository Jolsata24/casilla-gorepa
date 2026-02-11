<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center">
            <span class="w-2 h-8 bg-gorepa-500 rounded-full mr-3 shadow-[0_0_15px_rgba(87,193,199,0.4)]"></span>
            {{ __('Control de Notificaciones - GOREPA') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-slate-50 via-white to-gorepa-50/20 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- SECCIÓN DE ESTADÍSTICAS RÁPIDAS (REALES) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white/70 backdrop-blur-xl p-6 rounded-[2.5rem] border border-white shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Enviados</p>
                    <h4 class="text-3xl font-black text-gray-800">{{ $totalNotificaciones }}</h4>
                </div>
                <div class="bg-white/70 backdrop-blur-xl p-6 rounded-[2.5rem] border border-white shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pendientes</p>
                    <h4 class="text-3xl font-black text-amber-500">{{ $totalNotificaciones - $notificaciones->whereNotNull('fecha_lectura')->count() }}</h4>
                </div>
                <div class="bg-white/70 backdrop-blur-xl p-6 rounded-[2.5rem] border border-white shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Leídos</p>
                    <h4 class="text-3xl font-black text-green-500">{{ $notificaciones->whereNotNull('fecha_lectura')->count() }}</h4>
                </div>
                <div class="bg-gorepa-500 p-6 rounded-[2.5rem] shadow-xl shadow-gorepa-500/20 text-white flex flex-col justify-center">
                    <p class="text-[10px] font-bold opacity-80 uppercase tracking-widest mb-1">Ciudadanos</p>
                    <h4 class="text-3xl font-black">{{ $totalCiudadanos }}</h4>
                </div>
            </div>

            {{-- TABLA DE GESTIÓN AVANZADA --}}
            <div class="bg-white/80 backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.05)] border border-white overflow-hidden sm:rounded-[3rem]">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-white/50">
                    <div>
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Registro de Notificaciones</h3>
                        <p class="text-xs text-gray-400 font-medium">Trazabilidad completa de envíos y lecturas legales.</p>
                    </div>
                    <a href="{{ route('admin.crear') }}" class="px-6 py-3 bg-gorepa-500 text-white rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-gorepa-600 transition-all shadow-lg shadow-gorepa-500/30">
                        + Nueva Emisión
                    </a>
                </div>

                <div class="p-8 overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-4">
                        <thead>
                            <tr class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                                <th class="px-6 py-2 text-center w-10">Estado</th>
                                <th class="px-6 py-2">Ciudadano / DNI</th>
                                <th class="px-6 py-2">Asunto</th>
                                <th class="px-6 py-2">Enviado en</th>
                                <th class="px-6 py-2">Fecha Lectura</th>
                                <th class="px-6 py-2">IP Acceso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notificaciones as $n)
                            <tr class="bg-gray-50/50 hover:bg-white hover:shadow-lg transition-all duration-300 group">
                                {{-- ESTADO VISUAL --}}
                                <td class="px-6 py-5 rounded-l-[1.5rem] text-center">
                                    @if($n->fecha_lectura)
                                        <div class="w-3 h-3 bg-green-500 rounded-full mx-auto shadow-[0_0_8px_rgba(34,197,94,0.5)]" title="Leído"></div>
                                    @else
                                        <div class="w-3 h-3 bg-amber-400 rounded-full mx-auto animate-pulse" title="Pendiente"></div>
                                    @endif
                                </td>

                                {{-- CIUDADANO --}}
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-700 group-hover:text-gorepa-600 transition-colors">{{ $n->user->name }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">DNI: {{ $n->user->dni }}</span>
                                    </div>
                                </td>

                                {{-- ASUNTO --}}
                                <td class="px-6 py-5 text-sm text-gray-500 font-medium">
                                    {{ $n->asunto }}
                                </td>

                                {{-- FECHA ENVÍO --}}
                                <td class="px-6 py-5 text-xs text-gray-400 font-bold uppercase tracking-tighter">
                                    {{ $n->created_at->format('d/m/Y') }}
                                    <span class="block text-[9px] opacity-60 italic">{{ $n->created_at->format('H:i') }} hrs</span>
                                </td>

                                {{-- LÓGICA DE LECTURA --}}
                                <td class="px-6 py-5">
                                    @if($n->fecha_lectura)
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-green-600 uppercase italic">Leído</span>
                                            <span class="text-[10px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($n->fecha_lectura)->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-amber-500 font-bold uppercase italic tracking-widest">Sin lectura</span>
                                    @endif
                                </td>

                                {{-- IP DE LECTURA --}}
                                <td class="px-6 py-5 rounded-r-[1.5rem]">
                                    @if($n->ip_lectura)
                                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-mono border border-slate-200">
                                            {{ $n->ip_lectura }}
                                        </span>
                                    @else
                                        <span class="text-gray-200">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>