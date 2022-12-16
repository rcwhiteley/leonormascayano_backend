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

    public function getEvaluaciones(Request $request){
        $asignaturas = Asignaturas::with('evaluaciones')->find($request->asignatura_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Asignaturas obtenidas exitosamente',
            'data' => $asignaturas
        ], 200);
    }
}
