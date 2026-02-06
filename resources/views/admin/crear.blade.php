<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Envío - Secretaría General') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Mensaje de éxito --}}
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <h4 class="text-sm font-bold text-gray-700 mb-2">🔍 Buscar Ciudadano</h4>
    <form action="{{ route('admin.crear') }}" method="GET" class="flex gap-2">
        <x-text-input name="search" value="{{ $search }}" placeholder="Ingrese DNI o Nombre..." class="flex-1" />
        <x-secondary-button type="submit">
            Filtrar
        </x-secondary-button>
        @if($search)
            <a href="{{ route('admin.crear') }}" class="text-xs text-red-600 mt-2 hover:underline">Limpiar búsqueda</a>
        @endif
    </form>
</div>
                <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- 1. Seleccionar Ciudadano --}}
                    <div class="mb-4">
                        <x-input-label for="user_id" :value="__('Destinatario (Ciudadano)')" />
                        <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm">
                            <option value="">-- Seleccione un usuario --</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }} ({{ $usuario->email }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                    </div>

                    {{-- 2. Asunto --}}
                    <div class="mb-4">
                        <x-input-label for="asunto" :value="__('Asunto del Documento')" />
                        <x-text-input id="asunto" class="block mt-1 w-full" type="text" name="asunto" placeholder="Ej: Resolución Gerencial N° 123-2026" required />
                    </div>

                    {{-- 3. Mensaje (Opcional) --}}
                    <div class="mb-4">
                        <x-input-label for="mensaje" :value="__('Mensaje Adicional')" />
                        <textarea name="mensaje" id="mensaje" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    {{-- 4. Archivo PDF --}}
                    <div class="mb-6">
                        <x-input-label for="archivo" :value="__('Documento Digital (PDF)')" />
                        <input type="file" name="archivo" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" required />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button class="bg-red-700 hover:bg-red-800">
                            {{ __('Enviar Notificación Legal') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>