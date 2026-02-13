<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'dni' => ['required', 'string', 'size:8', 'unique:users'],
        'name' => ['required', 'string', 'max:255'],
        'apellido_paterno' => ['required', 'string', 'max:255'],
        'apellido_materno' => ['required', 'string', 'max:255'],
        // AGREGAR VALIDACIÓN DE CELULAR
        'celular' => ['required', 'string', 'max:15'], 
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // AGREGAR VALIDACIÓN DE DIRECCIÓN PARA EVITAR DATOS VACÍOS
        'departamento' => ['nullable', 'string'],
        'provincia' => ['nullable', 'string'],
        'distrito' => ['nullable', 'string'],
        'direccion' => ['nullable', 'string'],
    ]);

    $user = User::create([
        'dni' => $request->dni,
        'name' => $request->name,
        'apellido_paterno' => $request->apellido_paterno,
        'apellido_materno' => $request->apellido_materno,
        // AGREGAR EL CELULAR AQUÍ
        'celular' => $request->celular, 
        'departamento' => $request->departamento,
        'provincia' => $request->provincia,
        'distrito' => $request->distrito,
        'direccion' => $request->direccion,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'status' => 0,
        // IMPORTANTE: Definir is_admin explícitamente para evitar error si no tiene default en DB
        'is_admin' => 0, 
    ]);

    event(new Registered($user));

    return redirect()->route('login')->with('status', 'Su solicitud ha sido enviada. Espere aprobación.');
}
}