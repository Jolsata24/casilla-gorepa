<nav x-data="{ open: false }" 
     class="border-b border-gray-100 transition-colors duration-300 shadow-lg relative z-50
            {{ Auth::user()->is_admin ? 'bg-slate-900 border-slate-800' : 'bg-blue-900 border-blue-800' }}">

    {{-- LÓGICA PHP INTACTA --}}
    @php
        $notificacionesCount = 0;
        $tienePendientes = false;

        if (Auth::user()->is_admin) {
            $notificacionesCount = \App\Models\User::where('status', 0)->where('is_admin', false)->count();
            $tienePendientes = $notificacionesCount > 0;
            $textoNotificacion = $notificacionesCount . ' solicitud(es) pendiente(s)';
            $enlaceNotificacion = route('admin.peticiones');
        } else {
            $notificacionesCount = \App\Models\Notificacion::where('user_id', Auth::id())->whereNull('fecha_lectura')->count();
            $tienePendientes = $notificacionesCount > 0;
            $textoNotificacion = $notificacionesCount . ' documento(s) sin leer';
            $enlaceNotificacion = route('casilla.index');
        }
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            
            {{-- SECCIÓN IZQUIERDA --}}
            <div class="flex items-center gap-8">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-4 transition-opacity hover:opacity-90">
                        
                        {{-- AQUÍ ESTÁ EL CAMBIO DE LOGO --}}
                        <img src="{{ asset('logo-gorepa.png') }}" alt="GORE Pasco" class="block h-12 w-auto object-contain bg-white/10 rounded-lg p-1 backdrop-blur-sm">
                        
                        <div class="hidden md:block leading-tight">
                            <h1 class="text-sm font-black text-white uppercase tracking-widest">GORE Pasco</h1>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="h-2 w-2 rounded-full {{ Auth::user()->is_admin ? 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.6)]' : 'bg-green-400 shadow-[0_0_10px_rgba(74,222,128,0.6)]' }}"></span>
                                <span class="text-[10px] font-bold text-gray-300 uppercase tracking-wider">
                                    {{ Auth::user()->is_admin ? 'Panel Administrativo' : 'Casilla Electrónica' }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- NAVEGACIÓN (Enlaces blancos para resaltar sobre fondo oscuro) --}}
                <div class="hidden space-x-8 sm:-my-px sm:flex h-full items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="text-white hover:text-cyan-200 border-transparent hover:border-cyan-300 focus:border-white transition-all font-medium tracking-wide">
                        {{ __('Inicio') }}
                    </x-nav-link>
                    
                    @if(Auth::user()->is_admin)
                        <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" class="text-white hover:text-cyan-200 border-transparent hover:border-cyan-300 focus:border-white transition-all font-medium tracking-wide">
                            {{ __('Bandeja') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.peticiones')" :active="request()->routeIs('admin.peticiones')" class="text-white hover:text-cyan-200 border-transparent hover:border-cyan-300 focus:border-white transition-all font-medium tracking-wide">
                            {{ __('Accesos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.crear')" :active="request()->routeIs('admin.crear')" class="text-white hover:text-cyan-200 border-transparent hover:border-cyan-300 focus:border-white transition-all font-medium tracking-wide">
                            {{ __('Redactar') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.bitacora')" :active="request()->routeIs('admin.bitacora')" class="text-white hover:text-cyan-200 border-transparent hover:border-cyan-300 focus:border-white transition-all font-medium tracking-wide">
                            {{ __('Auditoría') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('casilla.index')" :active="request()->routeIs('casilla.index')" class="text-white hover:text-cyan-200 border-transparent hover:border-cyan-300 focus:border-white transition-all font-medium tracking-wide">
                            {{ __('Mis Documentos') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- SECCIÓN DERECHA --}}
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-3">
                
                {{-- Notificaciones --}}
                <x-dropdown align="right" width="72">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition-all focus:outline-none group">
                            @if($tienePendientes)
                                <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600 border-2 border-white"></span>
                                </span>
                            @endif
                            <svg class="w-6 h-6 transform group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <span class="text-xs text-gray-500 uppercase font-black">Novedades</span>
                            @if($tienePendientes) <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $notificacionesCount }}</span> @endif
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @if($tienePendientes)
                                <x-dropdown-link :href="$enlaceNotificacion" class="flex items-start gap-3 py-3 px-4 hover:bg-red-50">
                                    <div class="text-red-500 mt-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
                                    <div><p class="font-bold text-gray-800 text-xs">Atención Requerida</p><p class="text-[10px] text-gray-500 mt-0.5">Tienes <strong class="text-gray-700">{{ $textoNotificacion }}</strong>.</p></div>
                                </x-dropdown-link>
                            @else
                                <div class="px-4 py-6 text-center text-xs text-gray-400">Sin novedades</div>
                            @endif
                        </div>
                    </x-slot>
                </x-dropdown>

                {{-- Perfil --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 py-1 pl-1 pr-2 rounded-full hover:bg-white/10 transition border border-transparent focus:outline-none group">
                            <div class="w-8 h-8 rounded-full bg-white text-slate-900 flex items-center justify-center font-bold text-xs shadow-md group-hover:scale-105 transition-transform">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-left hidden lg:block">
                                <div class="text-xs font-bold text-white leading-none">{{ Auth::user()->name }}</div>
                            </div>
                            <svg class="fill-current h-4 w-4 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Perfil') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">{{ __('Salir') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Móvil --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-200 hover:text-white hover:bg-white/10 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MENÚ MÓVIL --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/10 bg-black/30 backdrop-blur-md">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-white/10">{{ __('Inicio') }}</x-responsive-nav-link>
            @if(Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" class="text-white hover:bg-white/10">{{ __('Bandeja') }}</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('casilla.index')" :active="request()->routeIs('casilla.index')" class="text-white hover:bg-white/10">{{ __('Mis Documentos') }}</x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-4 border-t border-white/10">
            <div class="px-4 text-white text-sm font-medium">{{ Auth::user()->name }}</div>
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400">{{ __('Cerrar Sesión') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>