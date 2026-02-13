<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;
use App\Models\Etiqueta;
use Illuminate\Support\Facades\Auth;

class CasillaController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filtro = $request->get('folder', 'inbox'); // Por defecto 'inbox'

        // 1. Obtener etiquetas del usuario para el sidebar
        $etiquetas = Etiqueta::where('user_id', $userId)->get();

        // 2. Query base
        $query = Notificacion::where('user_id', $userId);

        // 3. Aplicar filtros según la "carpeta" seleccionada
        switch ($filtro) {
            case 'inbox':
                // En recibidos mostramos los que NO tienen etiqueta y NO están borrados
                $query->whereNull('etiqueta_id');
                break;
            case 'starred':
                $query->where('es_destacado', true);
                break;
            case 'trash':
                $query->onlyTrashed();
                break;
            default:
                // Si es un número, es una etiqueta personalizada
                if (is_numeric($filtro)) {
                    $query->where('etiqueta_id', $filtro);
                }
                break;
        }

        $mensajes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('casilla.index', compact('mensajes', 'etiquetas', 'filtro'));
    }

    // Método para crear una nueva etiqueta
    public function crearEtiqueta(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:20']);
        
        Etiqueta::create([
            'user_id' => Auth::id(),
            'nombre' => $request->nombre,
            'color' => 'blue' // Color por defecto
        ]);

        return back()->with('success', 'Carpeta creada');
    }

    // Método para mover archivo a una etiqueta
    public function mover(Request $request, $id)
    {
        $notificacion = Notificacion::where('user_id', Auth::id())->findOrFail($id);
        
        // Si mandan 'inbox', ponemos null, si no, el ID de la etiqueta
        $etiquetaId = $request->etiqueta_id === 'inbox' ? null : $request->etiqueta_id;
        
        $notificacion->update(['etiqueta_id' => $etiquetaId]);

        return back()->with('success', 'Documento movido');
    }

    // Método para destacar (Estrellita)
    public function toggleDestacado($id)
    {
        $notificacion = Notificacion::where('user_id', Auth::id())->findOrFail($id);
        $notificacion->update(['es_destacado' => !$notificacion->es_destacado]);
        
        return back();
    }
}