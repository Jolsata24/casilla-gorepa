{{-- --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center">
            <span class="w-2 h-8 bg-gorepa-500 rounded-full mr-3 shadow-[0_0_15px_rgba(87,193,199,0.5)]"></span>
            {{ __('Configuración de Perfil Institucional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- SECCIÓN: INFORMACIÓN PERSONAL --}}
            <div class="p-8 sm:p-12 bg-white/80 backdrop-blur-xl shadow-[0_20px_60px_rgba(0,0,0,0.05)] border border-white sm:rounded-[3rem]">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- SECCIÓN: SEGURIDAD (CONTRASEÑA) --}}
            <div class="p-8 sm:p-12 bg-white/80 backdrop-blur-xl shadow-[0_20px_60px_rgba(0,0,0,0.05)] border border-white sm:rounded-[3rem]">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- SECCIÓN: ZONA DE PELIGRO (ELIMINAR) --}}
            <div class="p-8 sm:p-12 bg-red-50/30 backdrop-blur-xl shadow-sm border border-red-100 sm:rounded-[3rem]">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>