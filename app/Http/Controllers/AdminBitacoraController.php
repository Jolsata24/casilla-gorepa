<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class AdminBitacoraController extends Controller
{
    public function index()
    {
        // Traemos las bitácoras ordenadas
        $registros = Bitacora::with('user')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        // Retornamos la vista correcta
        return view('admin.bitacora', compact('registros'));
    }
}