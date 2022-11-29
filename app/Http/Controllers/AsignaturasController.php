<?php

namespace App\Http\Controllers;

use App\Models\Asignaturas;
use Illuminate\Http\Request;

class AsignaturasController extends Controller
{
    public function getAll(){
        $asignaturas = Asignaturas::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Asignaturas obtenidas exitosamente',
            'data' => $asignaturas
        ], 200);
    }
}
