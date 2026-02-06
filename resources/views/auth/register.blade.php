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
        const nombreInput = document.getElementById('name');
        const btn = document.getElementById('btn-validar-dni');

        if (dni.length !== 8) return alert('El DNI debe tener 8 dígitos');

        // Efecto visual "Cargando"
        btn.disabled = true;
        btn.innerText = 'Buscando...';
        nombreInput.value = 'Consultando RENIEC...';

        try {
            const response = await fetch(`/consulta-dni/${dni}`);
            const data = await response.json();

            if (data.success) {
                // ¡Éxito! Llenamos el nombre y bloqueamos el campo
                nombreInput.value = data.nombre_completo;
                nombreInput.readOnly = true; 
                nombreInput.classList.add('bg-gray-100'); // Color gris para indicar bloqueado
            } else {
                alert('DNI no encontrado o servicio no disponible');
                nombreInput.value = '';
                nombreInput.readOnly = false;
            }
        } catch (error) {
            console.error(error);
            alert('Error al conectar con el servidor');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Validar';
        }
    });
</script>