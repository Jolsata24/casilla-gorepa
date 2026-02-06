<nav x-data="{ open: false }" class="bg-red-800 border-b border-red-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('logo-gorepa.png') }}" alt="GOREPA" class="h-10 w-auto bg-white rounded-full p-1">
                        
                        <div class="leading-tight hidden md:block">
                            <h1 class="font-bold text-white text-lg">GOBIERNO REGIONAL PASCO</h1>
                            <span class="text-xs text-red-200 tracking-wider block">CASILLA ELECTRÓNICA OFICIAL</span>
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-gray-200">
                        {{ __('Inicio') }}
                    </x-nav-link>

                    @if(Auth::user()->is_admin)
                        {{-- MENÚ PARA EL ADMINISTRADOR --}}
                        <x-nav-link :href="route('admin.crear')" :active="request()->routeIs('admin.crear')" class="text-white hover:text-gray-200">
                            {{ __('📤 Enviar Notificación') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" class="text-white hover:text-gray-200">
                            {{ __('📋 Control de Lecturas') }}
                        </x-nav-link>
                    @else
                        {{-- MENÚ PARA EL CIUDADANO --}}
                        <x-nav-link :href="route('casilla.index')" :active="request()->routeIs('casilla.index')" class="text-white hover:text-gray-200">
                            {{ __('Mis Documentos') }}

                            {{-- Lógica del Badge (Contador) --}}
                            @php
                                $pendientes = \App\Models\Notificacion::where('user_id', auth()->id())
                                                ->whereNull('fecha_lectura')
                                                ->count();
                            @endphp

                            @if($pendientes > 0)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-white text-red-800 animate-pulse">
                                    {{ $pendientes }}
                                </span>
                            @endif
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-900 hover:bg-red-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col items-start text-left">
                                <span class="font-bold">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-red-200">{{ Auth::user()->dni ?? 'Sin DNI' }}</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mi Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-red-100 hover:text-white hover:bg-red-700 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-red-900">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">
                {{ __('Inicio') }}
            </x-responsive-nav-link>

            @if(Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('admin.crear')" :active="request()->routeIs('admin.crear')" class="text-white">
                    {{ __('Enviar Notificación') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" class="text-white">
                    {{ __('Control de Lecturas') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('casilla.index')" :active="request()->routeIs('casilla.index')" class="text-white font-bold">
                    {{ __('Mis Documentos') }} 
                    @if(isset($pendientes) && $pendientes > 0)
                        <span class="bg-white text-red-800 px-2 rounded-full text-xs ml-2">{{ $pendientes }}</span>
                    @endif
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-red-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-red-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-red-200 hover:text-white">
                    {{ __('Mi Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" class="text-red-200 hover:text-white"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>