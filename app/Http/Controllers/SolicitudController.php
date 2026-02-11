<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SolicitudController extends Controller
{
    // Muestra el formulario
    public function create()
    {
        return view('auth.solicitar-acceso');
    }

    // Guarda los datos básicos
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'apellido_paterno' => 'required|string',
            'apellido_materno' => 'required|string',
        ]);

        User::create([
            'dni' => $request->dni,
            'name' => $request->name,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            // Generamos una contraseña temporal aleatoria e inútil (porque está status 0)
            'password' => Hash::make(Str::random(30)), 
            'status' => 0, // Pendiente
            'is_admin' => 0
        ]);

        return redirect('/')->with('status', 'Solicitud enviada exitosamente. Se le notificará cuando se generen sus credenciales.');
    }
}