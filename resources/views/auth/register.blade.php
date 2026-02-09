<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mt-4">
    <x-input-label for="dni" :value="__('DNI')" />
    <div class="flex gap-2">
        <x-text-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni')" required autofocus maxlength="8" />
        <button type="button" id="btn-validar-dni" class="mt-1 px-4 bg-gray-800 text-white rounded-md hover:bg-gray-700">
            Validar
        </button>
    </div>
    <x-input-error :messages="$errors->get('dni')" class="mt-2" />
</div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="apellido_paterno" :value="__('Apellido Paterno')" />
        <x-text-input id="apellido_paterno" class="block mt-1 w-full bg-gray-50" type="text" name="apellido_paterno" readonly />
    </div>
    <div>
        <x-input-label for="apellido_materno" :value="__('Apellido Materno')" />
        <x-text-input id="apellido_materno" class="block mt-1 w-full bg-gray-50" type="text" name="apellido_materno" readonly />
    </div>
</div>

<div class="mt-4 grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="departamento" :value="__('Departamento')" />
        <x-text-input id="departamento" class="block mt-1 w-full bg-gray-50" type="text" name="departamento" readonly />
    </div>
    <div>
        <x-input-label for="provincia" :value="__('Provincia')" />
        <x-text-input id="provincia" class="block mt-1 w-full bg-gray-50" type="text" name="provincia" readonly />
    </div>
    <div>
        <x-input-label for="distrito" :value="__('Distrito')" />
        <x-text-input id="distrito" class="block mt-1 w-full bg-gray-50" type="text" name="distrito" readonly />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="direccion" :value="__('Dirección')" />
    <x-text-input id="direccion" class="block mt-1 w-full bg-gray-50" type="text" name="direccion" readonly />
</div>
        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
<script>
    document.getElementById('btn-validar-dni').addEventListener('click', async () => {
        const dni = document.getElementById('dni').value;
        const btn = document.getElementById('btn-validar-dni');

        if (dni.length !== 8) return alert('El DNI debe tener 8 dígitos');

        btn.disabled = true;
        btn.innerText = 'Buscando...';

        try {
            const response = await fetch(`/dni/info/${dni}`);
            const result = await response.json();

            if (result.success) {
                const info = result.data;
                // Mapeo de campos
                document.getElementById('name').value = info.nombres;
                document.getElementById('apellido_paterno').value = info.apellido_paterno;
                document.getElementById('apellido_materno').value = info.apellido_materno;
                document.getElementById('departamento').value = info.departamento;
                document.getElementById('provincia').value = info.provincia;
                document.getElementById('distrito').value = info.distrito;
                document.getElementById('direccion').value = info.direccion;
            } else {
                alert('DNI no encontrado');
            }
        } catch (error) {
            alert('Error en la validación');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Validar';
        }
    });
</script>