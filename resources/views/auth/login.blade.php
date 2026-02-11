<x-guest-layout>
    <div x-data="{ isRegister: false }" 
         class="relative w-full max-w-[1100px] min-h-[600px] flex items-center justify-center p-4">
        
        {{-- 1. CONTENEDOR DE TEXTO (El mensaje que cambia de lado) --}}
        <div class="absolute top-0 bottom-0 w-1/2 flex flex-col justify-center px-16 transition-all duration-700 ease-in-out z-10"
             :class="isRegister ? 'right-0 items-end text-right' : 'left-0 items-start text-left'">
            
            <div class="space-y-8 text-white drop-shadow-md max-w-md">
                {{-- Barra decorativa animada --}}
                <div class="w-24 h-2 bg-gradient-to-r from-gorepa-400 to-gorepa-200 rounded-full shadow-lg transition-all duration-500"
                     :class="isRegister ? 'ml-auto' : 'mr-auto'"></div>
                
                <template x-if="!isRegister">
                    <div class="animate-fade-in space-y-6">
                        <h1 class="text-5xl font-black leading-tight tracking-tight drop-shadow-xl">
                            Plataforma <span class="text-gorepa-300">Digital</span><br>GORE Pasco
                        </h1>
                        <p class="text-lg text-white/90 font-medium leading-relaxed">
                            Gestione sus notificaciones, expedientes y trámites de manera centralizada y segura.
                        </p>
                        <button @click="isRegister = true" class="group mt-4 px-8 py-3.5 border border-white/30 bg-white/10 hover:bg-white hover:text-gorepa-700 text-white rounded-2xl font-bold uppercase tracking-widest transition-all duration-300 backdrop-blur-sm shadow-lg flex items-center gap-3" :class="isRegister ? 'flex-row-reverse' : ''">
                            <span>Solicitar Acceso</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                    </div>
                </template>

                <template x-if="isRegister">
                    <div class="animate-fade-in space-y-6">
                        <h1 class="text-5xl font-black leading-tight tracking-tight drop-shadow-xl">
                            ¿Ya tiene <br>una cuenta?
                        </h1>
                        <p class="text-lg text-white/90 font-medium leading-relaxed">
                            Si ya cuenta con sus credenciales autorizadas, ingrese para revisar su bandeja.
                        </p>
                        <button @click="isRegister = false" class="group mt-4 px-8 py-3.5 border border-white/30 bg-white/10 hover:bg-white hover:text-gorepa-700 text-white rounded-2xl font-bold uppercase tracking-widest transition-all duration-300 backdrop-blur-sm shadow-lg flex items-center gap-3 ml-auto">
                            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            <span>Iniciar Sesión</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- 2. TARJETA DEL FORMULARIO (Se desliza físicamente) --}}
        {{-- Ajustamos la posición con 'left' y 'transform' para centrarla mejor --}}
        <div class="absolute top-1/2 -translate-y-1/2 w-[440px] transition-all duration-700 ease-[cubic-bezier(0.4,0,0.2,1)] z-20"
             :class="isRegister ? 'left-[10%] lg:left-[5%]' : 'left-[calc(90%-440px)] lg:left-[calc(95%-440px)]'">
            
            <div class="relative bg-white/90 backdrop-blur-2xl border border-white/60 rounded-[2.5rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] p-10 overflow-hidden">
                
                {{-- Decoración de fondo en la tarjeta --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-gorepa-100 rounded-full blur-3xl -mr-10 -mt-10 opacity-60"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-100 rounded-full blur-3xl -ml-10 -mb-10 opacity-60"></div>

                {{-- A. FORMULARIO LOGIN --}}
                <div x-show="!isRegister" x-transition:enter="transition ease-out duration-500 delay-200" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="relative z-10">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gorepa-50 rounded-xl text-gorepa-600 mb-4 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight">BIENVENIDO</h2>
                        <p class="text-sm text-gray-500 font-medium mt-1">Ingrese sus credenciales de acceso</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        {{-- Email --}}
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">Usuario</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-gorepa-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                </span>
                                <input type="email" name="email" required class="w-full pl-11 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-2xl focus:bg-white focus:border-gorepa-500 focus:ring-4 focus:ring-gorepa-500/10 transition-all outline-none font-medium text-gray-700 placeholder-gray-400" placeholder="ejemplo@regionpasco.gob.pe">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">Contraseña</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-gorepa-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </span>
                                <input type="password" name="password" required class="w-full pl-11 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-2xl focus:bg-white focus:border-gorepa-500 focus:ring-4 focus:ring-gorepa-500/10 transition-all outline-none font-medium text-gray-700 placeholder-••••••••">
                            </div>
                            <div class="flex justify-end mt-2">
                                @if (Route::has('password.request'))
                                    <a class="text-xs font-bold text-gorepa-600 hover:text-gorepa-800 transition" href="{{ route('password.request') }}">¿Olvidó su clave?</a>
                                @endif
                            </div>
                        </div>

                        <button class="w-full py-4 bg-gradient-to-r from-gorepa-600 to-gorepa-500 hover:to-gorepa-600 text-white font-bold rounded-2xl shadow-lg shadow-gorepa-500/30 transform transition hover:-translate-y-0.5 active:translate-y-0 mt-2">
                            INGRESAR
                        </button>
                    </form>
                </div>

                {{-- B. FORMULARIO SOLICITUD --}}
                <div x-show="isRegister" x-transition:enter="transition ease-out duration-500 delay-200" x-transition:enter-start="opacity-0 -translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="relative z-10" style="display: none;">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-50 rounded-xl text-blue-600 mb-3 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight">NUEVA CUENTA</h2>
                        <p class="text-sm text-gray-500 font-medium">Solicitud de Casilla Electrónica</p>
                    </div>

                    <form method="POST" action="{{ route('solicitud.store') }}" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <input type="text" name="dni" placeholder="DNI" maxlength="8" class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white focus:border-gorepa-500 focus:ring-2 focus:ring-gorepa-500/20 text-sm font-medium transition-all text-center">
                            </div>
                            <div class="col-span-2">
                                <input type="text" name="name" placeholder="Nombres" class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white focus:border-gorepa-500 focus:ring-2 focus:ring-gorepa-500/20 text-sm font-medium transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="apellido_paterno" placeholder="Ap. Paterno" class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white focus:border-gorepa-500 focus:ring-2 focus:ring-gorepa-500/20 text-sm font-medium transition-all">
                            <input type="text" name="apellido_materno" placeholder="Ap. Materno" class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white focus:border-gorepa-500 focus:ring-2 focus:ring-gorepa-500/20 text-sm font-medium transition-all">
                        </div>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                            <input type="email" name="email" placeholder="Correo Electrónico Personal" class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white focus:border-gorepa-500 focus:ring-2 focus:ring-gorepa-500/20 text-sm font-medium transition-all">
                        </div>

                        <button class="w-full py-4 bg-gray-900 hover:bg-black text-white font-bold rounded-2xl shadow-lg transform transition hover:-translate-y-0.5 active:translate-y-0 mt-2">
                            ENVIAR SOLICITUD
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</x-guest-layout>