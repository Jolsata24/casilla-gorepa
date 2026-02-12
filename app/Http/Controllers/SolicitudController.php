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
        // 1. Validación con Mensajes Personalizados
        $validated = $request->validate([
            'dni' => 'required|string|size:8|unique:users,dni',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'apellido_paterno' => 'required|string',
            'apellido_materno' => 'required|string',
            'celular' => 'nullable|string|max:15',
        ], [
            // Mensajes de error específicos en español
            'dni.unique' => 'Este DNI ya se encuentra registrado en el sistema. Intente iniciar sesión.',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
            'email.unique' => 'Este correo electrónico ya está siendo usado por otro usuario.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'required' => 'El campo :attribute es obligatorio.',
        ]);

        try {
            // 2. Intentar crear el usuario
            User::create([
                'dni' => $request->dni,
                'name' => $request->name,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'email' => $request->email,
                'celular' => $request->celular,
                'password' => Hash::make(Str::random(30)), // Clave temporal
                'status' => 0,    // Pendiente
                'is_admin' => 0,
            ]);

            // 3. Éxito: Regresar al formulario con mensaje verde
            return back()->with('status', '¡Solicitud enviada correctamente! Se le notificará al correo cuando sus credenciales sean generadas.');

        } catch (\Exception $e) {
            // 4. Error Inesperado (BD caída, etc): Regresar con mensaje rojo
            // Log::error("Error al registrar solicitud: " . $e->getMessage()); // Opcional para debug interno
            
            return back()
                ->withInput() // Mantiene lo que el usuario escribió
                ->with('error', 'Hubo un error interno al intentar registrar su solicitud. Por favor intente más tarde.');
        }
    }
}