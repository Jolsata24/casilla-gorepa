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

    public function store(Request $request)
    {   
        
        // 1. Validación (Aseguramos que celular sea obligatorio)
        $validated = $request->validate([
        'dni' => 'required|string|size:8|unique:users,dni',
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'apellido_paterno' => 'required|string',
        'apellido_materno' => 'required|string',
        'celular' => 'required|string|max:15', // Asegúrate que este input exista en el formulario
        
        // CAMBIO IMPORTANTE: Ponlos como 'nullable' para evitar rebotes silenciosos
        'departamento' => 'nullable|string',
        'provincia' => 'nullable|string',
        'distrito' => 'nullable|string',
        'direccion' => 'nullable|string',
    ]);

    try {
        $user = User::create([
            'dni' => $request->dni,
            'name' => $request->name,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'celular' => $request->celular,
            
            // Usamos el operador null coalescing (??) por seguridad
            'departamento' => $request->departamento ?? 'No registrado',
            'provincia' => $request->provincia ?? 'No registrado',
            'distrito' => $request->distrito ?? 'No registrado',
            'direccion' => $request->direccion ?? 'No registrado',
            
            'password' => Hash::make(Str::random(30)),
            'status' => 0,
            'is_admin' => 0,
        ]);

        return redirect()->route('login')->with('status', '¡Solicitud enviada! Espere aprobación.');

    } catch (\Exception $e) {
        // Esto te mostrará el error real en pantalla si la BD falla
        return back()->withInput()->with('error', 'Error BD: ' . $e->getMessage());
    }
    }
}