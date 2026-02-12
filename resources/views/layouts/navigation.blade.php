<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-lg border-b border-gray-100 sticky top-0 z-50 transition-all duration-300 shadow-sm">
    
    {{-- LÓGICA PHP: Contar pendientes para las notificaciones --}}
    @php
        $notificacionesCount = 0;
        $tienePendientes = false;

        if (Auth::user()->is_admin) {
            // ADMIN: Cuenta usuarios con status 0 (Solicitudes Pendientes)
            $notificacionesCount = \App\Models\User::where('status', 0)->where('is_admin', false)->count();
            $tienePendientes = $notificacionesCount > 0;
            $textoNotificacion = $notificacionesCount . ' solicitud(es) pendiente(s)';
            $enlaceNotificacion = route('admin.peticiones');
        } else {
            // USUARIO: Cuenta notificaciones sin leer (fecha_lectura NULL)
            $notificacionesCount = \App\Models\Notificacion::where('user_id', Auth::id())->whereNull('fecha_lectura')->count();
            $tienePendientes = $notificacionesCount > 0;
            $textoNotificacion = $notificacionesCount . ' documento(s) sin leer';
            $enlaceNotificacion = route('casilla.index');
        }
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            
            {{-- SECCIÓN IZQUIERDA: LOGO Y NAVEGACIÓN --}}
            <div class="flex items-center gap-8">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 transition-opacity hover:opacity-80">
                        <img src="{{ asset('logo-gorepa.png') }}" alt="Logo" class="block h-10 w-auto">
                        <div class="hidden md:block leading-tight">
                            <h1 class="text-xs font-black text-gray-900 uppercase tracking-widest">GORE Pasco</h1>
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                    {{ Auth::user()->is_admin ? 'Panel Administrativo' : 'Casilla Electrónica' }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:flex h-full items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold tracking-wide text-xs uppercase">
                        {{ __('Inicio') }}
                    </x-nav-link>
                    
                    @if(Auth::user()->is_admin)
                        {{-- CORRECCIÓN: AGREGADO ENLACE A BANDEJA DE SALIDA --}}
                        <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" class="font-bold tracking-wide text-xs uppercase">
                            {{ __('Bandeja / Historial') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.peticiones')" :active="request()->routeIs('admin.peticiones')" class="font-bold tracking-wide text-xs uppercase">
                            {{ __('Accesos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.crear')" :active="request()->routeIs('admin.crear')" class="font-bold tracking-wide text-xs uppercase">
                            {{ __('Redactar') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('casilla.index')" :active="request()->routeIs('casilla.index')" class="font-bold tracking-wide text-xs uppercase">
                            {{ __('Mis Documentos') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- SECCIÓN DERECHA: HERRAMIENTAS Y PERFIL --}}
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-2">
                
                {{-- 1. BOTÓN DE NOTIFICACIONES (Campanita) --}}
                <x-dropdown align="right" width="72">
                    <x-slot name="trigger">
                        <button class="relative p-2.5 text-gray-400 hover:text-gorepa-600 hover:bg-gray-50 rounded-full transition-all focus:outline-none group">
                            {{-- Indicador de Actividad --}}
                            @if($tienePendientes)
                                <span class="absolute top-2.5 right-2.5 flex h-2.5 w-2.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border-2 border-white"></span>
                                </span>
                            @endif
                            <svg class="w-6 h-6 transform group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <span class="text-xs text-gray-500 uppercase font-black tracking-widest">Novedades</span>
                            @if($tienePendientes)
                                <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $notificacionesCount }}</span>
                            @endif
                        </div>
                        
                        <div class="max-h-64 overflow-y-auto">
                            @if($tienePendientes)
                                <x-dropdown-link :href="$enlaceNotificacion" class="flex items-start gap-4 py-4 px-4 hover:bg-red-50/50 transition border-l-4 border-red-500">
                                    <div class="shrink-0 bg-red-100 p-2 rounded-lg text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Atención Requerida</p>
                                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Tienes <strong class="text-gray-700">{{ $textoNotificacion }}</strong> que requieren tu revisión inmediata.</p>
                                        <p class="text-[10px] text-gray-400 mt-2 font-medium">Hace un momento</p>
                                    </div>
                                </x-dropdown-link>
                            @else
                                <div class="px-4 py-8 text-center opacity-50">
                                    <div class="mx-auto w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Todo está al día</p>
                                    <p class="text-xs text-gray-400">No tienes notificaciones nuevas</p>
                                </div>
                            @endif
                        </div>
                    </x-slot>
                </x-dropdown>

                {{-- 2. BOTÓN DE MODIFICACIONES / AJUSTES (Engranaje) --}}
                <div class="relative" x-data="{ tooltip: false }">
                    <a href="{{ route('profile.edit') }}" 
                       @mouseenter="tooltip = true" @mouseleave="tooltip = false"
                       class="p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-full transition-all focus:outline-none flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </a>
                    {{-- Tooltip sencillo --}}
                    <div x-show="tooltip" class="absolute top-full mt-2 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-[10px] font-bold rounded shadow-lg whitespace-nowrap z-50" style="display: none;">
                        Modificar Perfil
                    </div>
                </div>

                {{-- Separador vertical --}}
                <div class="h-8 w-px bg-gray-200 mx-2"></div>

                {{-- 3. PERFIL DE USUARIO (Con Avatar) --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 py-1 pl-1 pr-2 rounded-full hover:bg-gray-50 transition border border-transparent hover:border-gray-100 focus:outline-none group">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 text-white flex items-center justify-center font-bold text-sm shadow-md ring-2 ring-white group-hover:ring-gorepa-100 transition-all">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            
                            <div class="text-left hidden lg:block">
                                <div class="text-sm font-bold text-gray-700 leading-none group-hover:text-gorepa-700 transition-colors">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] font-semibold text-gray-400 uppercase mt-0.5 tracking-wide">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>

                            <svg class="fill-current h-4 w-4 text-gray-300 group-hover:text-gray-500 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                            <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Cuenta</p>
                            <p class="text-xs font-medium text-gray-600 truncate">{{ Auth::user()->name }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="group flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gorepa-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ __('Modificar Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 hover:bg-red-50 flex items-center gap-2 border-t border-gray-50 mt-1">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                {{ __('Salir del Sistema') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MENÚ MÓVIL (Responsive) --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100 bg-white shadow-lg">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Inicio') }}
            </x-responsive-nav-link>
            
            @if(Auth::user()->is_admin)
                {{-- CORRECCIÓN: AGREGADO ENLACE MÓVIL A BANDEJA DE SALIDA --}}
                <x-responsive-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')">
                    {{ __('Bandeja / Historial') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.peticiones')" :active="request()->routeIs('admin.peticiones')" class="flex justify-between items-center">
                    {{ __('Accesos / Peticiones') }}
                    @if($tienePendientes)
                         <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $notificacionesCount }}</span>
                    @endif
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.crear')" :active="request()->routeIs('admin.crear')">
                    {{ __('Redactar Notificación') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('casilla.index')" :active="request()->routeIs('casilla.index')" class="flex justify-between items-center">
                    {{ __('Mis Documentos') }}
                    @if($tienePendientes)
                         <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $notificacionesCount }}</span>
                    @endif
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-4 border-t border-gray-100 bg-gray-50/50">
            <div class="px-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-800 text-white flex items-center justify-center font-bold shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')" class="rounded-lg">
                    {{ __('Modificar Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 rounded-lg hover:bg-red-50">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>