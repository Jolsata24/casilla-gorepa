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
        // 1. Validación (Agregamos la validación del documento)
        $request->validate([
            'tipo_documento' => 'required|in:DNI,RUC',
            'dni' => 'nullable|required_if:tipo_documento,DNI|string|size:8|unique:users,dni',
            'apellido_paterno' => 'nullable|required_if:tipo_documento,DNI|string',
            'apellido_materno' => 'nullable|required_if:tipo_documento,DNI|string',
            'ruc' => 'nullable|required_if:tipo_documento,RUC|string|size:11|unique:users,ruc',
            'razon_social' => 'nullable|required_if:tipo_documento,RUC|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'celular' => 'required|string|max:15',
            'departamento' => 'nullable|string',
            'provincia' => 'nullable|string',
            'distrito' => 'nullable|string',
            'direccion' => 'nullable|string',
            // VALIDACIÓN DEL ARCHIVO
            'documento_confianza' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);

        try {
            // 2. Subida del Archivo
            $rutaDocumento = null;
            if ($request->hasFile('documento_confianza')) {
                // Se guarda en storage/app/public/documentos_confianza
                $rutaDocumento = $request->file('documento_confianza')->store('documentos_confianza', 'public');
            }

            // 3. Creación del Usuario
            $user = User::create([
                'tipo_documento' => $request->tipo_documento,
                'ruc' => $request->ruc,
                'razon_social' => $request->razon_social,
                'dni' => $request->dni,
                'name' => $request->name,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'email' => $request->email,
                'celular' => $request->celular,
                'departamento' => $request->departamento ?? 'No registrado',
                'provincia' => $request->provincia ?? 'No registrado',
                'distrito' => $request->distrito ?? 'No registrado',
                'direccion' => $request->direccion ?? 'No registrado',
                
                // GUARDAR LA RUTA EN LA BD
                'documento_confianza' => $rutaDocumento, 
                
                'password' => Hash::make(Str::random(30)),
                'status' => 0, 
                'is_admin' => 0,
            ]);

            return redirect()->route('login')->with('status', '¡Solicitud enviada exitosamente! El administrador evaluará sus datos.');

        } catch (\Exception $e) {
            Log::error('Error al registrar solicitud: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error BD: No se pudo crear la solicitud.');
        }
    }
}