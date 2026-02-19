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
use Illuminate\Support\Str; // <--- AGREGA ESTO ARRIBA
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
        'tipo_documento' => ['required', 'in:DNI,RUC'],
        
        'dni' => ['nullable', 'required_if:tipo_documento,DNI', 'string', 'size:8', 'unique:users,dni'],
        'apellido_paterno' => ['nullable', 'required_if:tipo_documento,DNI', 'string', 'max:255'],
        'apellido_materno' => ['nullable', 'required_if:tipo_documento,DNI', 'string', 'max:255'],
        
        'ruc' => ['nullable', 'required_if:tipo_documento,RUC', 'string', 'size:11', 'unique:users,ruc'],
        'razon_social' => ['nullable', 'required_if:tipo_documento,RUC', 'string', 'max:255'],

        'name' => ['required', 'string', 'max:255'], 
        'celular' => ['required', 'string', 'max:15'], 
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        
        // ¡Se eliminó la validación de password y password_confirmation!
        
        'departamento' => ['nullable', 'string'],
        'provincia' => ['nullable', 'string'],
        'distrito' => ['nullable', 'string'],
        'direccion' => ['nullable', 'string'],
    ]);

    // Generamos una contraseña aleatoria de 16 caracteres segura por defecto
    $passwordTemporal = Str::random(16);

    $user = User::create([
        'tipo_documento' => $request->tipo_documento,
        'ruc' => $request->ruc,
        'razon_social' => $request->razon_social,
        'dni' => $request->dni,
        'name' => $request->name,
        'apellido_paterno' => $request->apellido_paterno,
        'apellido_materno' => $request->apellido_materno,
        'celular' => $request->celular, 
        'departamento' => $request->departamento,
        'provincia' => $request->provincia,
        'distrito' => $request->distrito,
        'direccion' => $request->direccion,
        'email' => $request->email,
        'password' => Hash::make($passwordTemporal), // <--- SE GUARDA LA TEMPORAL
        'status' => 0, // <--- Nace inactivo, esperando al admin
        'is_admin' => 0, 
    ]);

    event(new Registered($user));

    return redirect()->route('login')->with('status', 'Su solicitud ha sido enviada. El administrador evaluará sus datos y le enviará sus credenciales al correo ingresado.');
}
}