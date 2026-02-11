{{-- --}}
<section>
    <header class="mb-6">
        <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">
            {{ __('Datos de Identidad') }}
        </h3>
        <p class="mt-1 text-sm text-gray-500 font-medium">
            {{ __("Actualice la información de su cuenta y su dirección de correo institucional.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-gray-50 space-y-6">
            {{-- Campo Nombre --}}
            <div>
                <x-input-label for="name" :value="__('Nombre Completo')" class="text-[10px] font-bold uppercase text-gray-400 mb-1 ml-1" />
                <x-text-input id="name" name="name" type="text" class="block w-full border-gray-100 bg-white focus:border-gorepa-500 focus:ring-gorepa-500 rounded-2xl py-3 shadow-sm transition-all" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            {{-- Campo Email --}}
            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" class="text-[10px] font-bold uppercase text-gray-400 mb-1 ml-1" />
                <x-text-input id="email" name="email" type="email" class="block w-full border-gray-100 bg-white focus:border-gorepa-500 focus:ring-gorepa-500 rounded-2xl py-3 shadow-sm transition-all" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-8 py-3 bg-gorepa-500 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-gorepa-500/30 hover:bg-gorepa-600 transition-all transform active:scale-95">
                {{ __('Guardar Cambios') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-xs font-bold text-green-600 animate-pulse">{{ __('¡Cambios guardados con éxito!') }}</p>
            @endif
        </div>
    </form>
</section>