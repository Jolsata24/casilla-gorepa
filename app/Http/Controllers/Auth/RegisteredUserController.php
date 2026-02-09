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
    // app/Http/Controllers/Auth/RegisteredUserController.php
public function store(Request $request)
{
    $request->validate([
        'dni' => ['required', 'string', 'size:8', 'unique:users'], // Validación de unicidad
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
    'dni' => $request->dni,
    'name' => $request->name,
    'apellido_paterno' => $request->apellido_paterno,
    'apellido_materno' => $request->apellido_materno,
    'departamento' => $request->departamento,
    'provincia' => $request->provincia,
    'distrito' => $request->distrito,
    'direccion' => $request->direccion,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);
    // ... resto del código
}
}
