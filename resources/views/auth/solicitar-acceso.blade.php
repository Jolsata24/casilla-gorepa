<x-guest-layout>
    <div class="p-8">
        <h2 class="text-2xl font-black text-gray-800 uppercase mb-2">Solicitud de Casilla</h2>
        <p class="text-sm text-gray-500 mb-6">Regístrese para que un administrador valide sus datos.</p>

        <form method="POST" action="{{ route('solicitud.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="dni" :value="__('DNI')" />
                <x-text-input id="dni" class="block mt-1 w-full rounded-2xl border-gray-100" type="text" name="dni" required autofocus />
            </div>

            <div>
                <x-input-label for="name" :value="__('Nombre Completo')" />
                <x-text-input id="name" class="block mt-1 w-full rounded-2xl border-gray-100" type="text" name="name" required />
            </div>

            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full rounded-2xl border-gray-100" type="email" name="email" required />
            </div>

            <x-primary-button class="w-full justify-center bg-gorepa-500 hover:bg-gorepa-600 rounded-2xl py-3">
                {{ __('Enviar Solicitud') }}
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>