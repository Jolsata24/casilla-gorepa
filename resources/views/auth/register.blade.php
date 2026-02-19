<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mt-4">
            <x-input-label for="tipo_documento" :value="__('Tipo de Usuario')" />
            <select id="tipo_documento" name="tipo_documento" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="DNI" {{ old('tipo_documento') == 'DNI' ? 'selected' : '' }}>Persona Natural (DNI)</option>
                <option value="RUC" {{ old('tipo_documento') == 'RUC' ? 'selected' : '' }}>Persona Jurídica (RUC)</option>
            </select>
        </div>

        <div id="bloque_dni" class="mt-4">
            <x-input-label for="dni" :value="__('DNI')" />
            <div class="flex gap-2">
                <x-text-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni')" maxlength="8" placeholder="Ingrese DNI" />
                <button type="button" id="btn-validar-dni" class="mt-1 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">Validar</button>
            </div>
            <x-input-error :messages="$errors->get('dni')" class="mt-2" />
        </div>

        <div id="bloque_ruc" class="mt-4 hidden">
            <x-input-label for="ruc" :value="__('RUC')" />
            <div class="flex gap-2">
                <x-text-input id="ruc" class="block mt-1 w-full" type="text" name="ruc" :value="old('ruc')" maxlength="11" placeholder="Ingrese RUC de la Empresa" />
                <button type="button" id="btn-validar-ruc" class="mt-1 px-4 py-2 bg-indigo-800 text-white rounded-md hover:bg-indigo-700">Validar RUC</button>
            </div>
            <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
            
            <div class="mt-4">
                <x-input-label for="razon_social" :value="__('Razón Social')" />
                <x-text-input id="razon_social" class="block mt-1 w-full bg-gray-50" type="text" name="razon_social" :value="old('razon_social')" readonly />
                <x-input-error :messages="$errors->get('razon_social')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label id="label_name" for="name" :value="__('Nombres (Persona Natural)')" />
            <x-text-input id="name" class="block mt-1 w-full bg-gray-50" type="text" name="name" :value="old('name')" required readonly />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div id="bloque_apellidos" class="mt-4 grid grid-cols-2 gap-4">
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

        <div class="mt-4">
            <x-input-label for="celular" :value="__('Celular de Contacto')" />
            <x-text-input id="celular" class="block mt-1 w-full" type="text" name="celular" :value="old('celular')" required placeholder="Ej: 987654321" />
            <x-input-error :messages="$errors->get('celular')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Registrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    // Lógica para alternar entre DNI y RUC
    const selectTipo = document.getElementById('tipo_documento');
    const bloqueDNI = document.getElementById('bloque_dni');
    const bloqueRUC = document.getElementById('bloque_ruc');
    const bloqueApellidos = document.getElementById('bloque_apellidos');
    const labelName = document.getElementById('label_name');
    const inputName = document.getElementById('name');

    selectTipo.addEventListener('change', function() {
        if (this.value === 'DNI') {
            bloqueDNI.classList.remove('hidden');
            bloqueApellidos.classList.remove('hidden');
            bloqueRUC.classList.add('hidden');
            labelName.innerText = 'Nombres (Persona Natural)';
            inputName.readOnly = true; // Se llena con la API
            
            // Limpiar valores para no enviar datos mezclados
            document.getElementById('ruc').value = '';
            document.getElementById('razon_social').value = '';
        } else {
            bloqueRUC.classList.remove('hidden');
            bloqueDNI.classList.add('hidden');
            bloqueApellidos.classList.add('hidden');
            labelName.innerText = 'Nombre del Representante Legal';
            inputName.readOnly = false; // El representante se ingresa manual si la API no lo trae
            
            // Limpiar valores de DNI
            document.getElementById('dni').value = '';
            document.getElementById('apellido_paterno').value = '';
            document.getElementById('apellido_materno').value = '';
        }
    });

    // Mantén aquí tu script original de btn-validar-dni ...
    // (Pega el script de fetch(`/dni/info/${dni}`) que ya tenías)
</script>
<script>
    document.getElementById('btn-validar-dni').addEventListener('click', async () => {
        const dniInput = document.getElementById('dni');
        const dni = dniInput.value.trim();
        const btn = document.getElementById('btn-validar-dni');

        if (dni.length !== 8) {
            alert('El DNI debe tener 8 dígitos');
            dniInput.focus();
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Buscando...';

        try {
            const response = await fetch(`/dni/info/${dni}`);
            
            if (!response.ok) throw new Error('Error en la red');

            const result = await response.json();

            if (result.success) {
                const info = result.data;
                
                // Llenar campos
                document.getElementById('name').value = info.nombres || '';
                document.getElementById('apellido_paterno').value = info.apellido_paterno || '';
                document.getElementById('apellido_materno').value = info.apellido_materno || '';
                document.getElementById('departamento').value = info.departamento || '';
                document.getElementById('provincia').value = info.provincia || '';
                document.getElementById('distrito').value = info.distrito || '';
                document.getElementById('direccion').value = info.direccion || '';
                
            } else {
                alert('DNI no encontrado en RENIEC. Por favor verifique o ingrese los datos manualmente.');
                // Habilitar campos si no se encuentra (opcional, si quieres permitir llenado manual)
                // document.getElementById('name').readOnly = false;
                // etc...
            }
        } catch (error) {
            console.error(error);
            alert('Ocurrió un error al validar el DNI. Intente nuevamente.');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Validar';
        }
    });
</script>