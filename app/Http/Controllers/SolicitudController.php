<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    public function create()
    {
        return view('auth.solicitar-acceso');
    }

    // En app/Http/Controllers/SolicitudController.php

public function store(Request $request)
{
    // 1. Validación: Aceptamos que los campos de dirección vengan o no
    $validated = $request->validate([
        'dni' => 'required|string|size:8|unique:users,dni',
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'apellido_paterno' => 'required|string',
        'apellido_materno' => 'required|string',
        'celular' => 'required|string|max:15', // Celular obligatorio
        'departamento' => 'nullable|string',
        'provincia' => 'nullable|string',
        'distrito' => 'nullable|string',
        'direccion' => 'nullable|string',
    ], [
        'dni.unique' => 'Este DNI ya está registrado. Por favor inicie sesión.',
        'dni.size' => 'El DNI debe tener 8 dígitos.',
        'email.unique' => 'El correo ya está en uso.',
        'required' => 'El campo :attribute es obligatorio.',
    ]);

    try {
        // 2. Crear usuario con TODOS los datos (¡Aquí faltaban las líneas!)
        User::create([
            'dni' => $request->dni,
            'name' => $request->name,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'celular' => $request->celular,
            
            // --- INICIO DE LAS LÍNEAS FALTANTES ---
            'departamento' => $request->departamento,
            'provincia' => $request->provincia,
            'distrito' => $request->distrito,
            'direccion' => $request->direccion,
            // --- FIN DE LAS LÍNEAS FALTANTES ---
            
            'password' => Hash::make(Str::random(30)),
            'status' => 0,    // 0 = Pendiente de aprobación
            'is_admin' => 0,
        ]);

        return back()->with('status', '¡Solicitud enviada correctamente!');

    } catch (\Exception $e) {
        // Esto sirve para ver el error real si falla
        \Illuminate\Support\Facades\Log::error("Error al registrar: " . $e->getMessage());
        return back()->withInput()->with('error', 'Error interno: ' . $e->getMessage());
    }
}
}