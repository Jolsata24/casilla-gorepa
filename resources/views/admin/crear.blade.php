<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center">
            <span class="w-2 h-8 bg-gorepa-500 rounded-full mr-3 shadow-[0_0_15px_rgba(87,193,199,0.5)]"></span>
            {{ __('Nueva Notificación Electrónica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Tarjeta con efecto cristal y sombras suaves --}}
            <div class="bg-white/80 backdrop-blur-xl shadow-[0_20px_60px_rgba(0,0,0,0.05)] border border-white overflow-hidden sm:rounded-[2.5rem] p-10">
                
                <div class="mb-8 border-b border-gray-100 pb-6">
                    <p class="text-sm text-gray-500 font-medium">Complete el formulario para emitir una nueva notificación oficial a través de la casilla digital.</p>
                </div>

                <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    {{-- Sección: Datos del Ciudadano --}}
                    <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-gray-50">
                        <div class="flex items-center mb-4 text-gorepa-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <h4 class="text-xs font-black uppercase tracking-widest">Información del Destinatario</h4>
                        </div>
                        
                        {{-- --}}
<div>
    <x-input-label for="user_id" :value="__('Seleccionar Ciudadano')" class="text-[10px] font-bold uppercase text-gray-400 mb-2 ml-1" />
    <select name="user_id" id="user_id" required class="w-full border-gray-100 bg-white focus:border-gorepa-500 focus:ring-gorepa-500 rounded-2xl py-3 shadow-sm transition-all duration-300 text-sm font-medium text-gray-700">
        <option value="" disabled selected>-- Seleccione un ciudadano de la lista --</option>
        
        {{-- CICLO PARA MOSTRAR LOS USUARIOS REGISTRADOS --}}
        @foreach($usuarios as $usuario)
            <option value="{{ $usuario->id }}">
                {{ $usuario->name }} (DNI: {{ $usuario->dni }})
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
</div>
                    </div>

                    {{-- Sección: Documento --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <x-input-label for="asunto" :value="__('Asunto del Documento')" class="text-[10px] font-bold uppercase text-gray-400 mb-2 ml-1" />
                            <x-text-input id="asunto" name="asunto" type="text" class="block w-full border-gray-100 bg-slate-50/30 focus:bg-white rounded-2xl" placeholder="Ej: Resolución Ejecutiva N° 123" required />
                        </div>

                        <div>
                            <x-input-label for="archivo" :value="__('Cargar PDF Oficial')" class="text-[10px] font-bold uppercase text-gray-400 mb-2 ml-1" />
                            <div class="relative group">
                                <input type="file" name="archivo" id="archivo" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="border-2 border-dashed border-gray-200 group-hover:border-gorepa-400 rounded-2xl p-3 flex items-center justify-center bg-white transition-all">
                                    <span class="text-xs text-gray-500 font-bold uppercase group-hover:text-gorepa-600">Adjuntar archivo .pdf</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botón de Acción --}}
                    <div class="pt-6">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-4 bg-gorepa-500 text-white rounded-[1.5rem] font-black uppercase text-sm tracking-widest shadow-xl shadow-gorepa-500/30 hover:bg-gorepa-600 transition-all transform hover:-translate-y-1 active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            {{ __('Emitir Notificación Digital') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>