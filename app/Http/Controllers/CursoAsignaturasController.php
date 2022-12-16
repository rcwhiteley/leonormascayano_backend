<?php

namespace App\Http\Controllers;

use App\Models\AsignaturaCurso;
use Illuminate\Http\Request;

class CursoAsignaturasController extends Controller
{
    function addAsignatura(Request $request){
        error_log(json_encode($request->all()));
        try {
            $cursoId = $request->id;
            $request->validate([
                'asignatura_id' => 'required|integer',
            ]);
            $asignaturaCurso = new AsignaturaCurso([
                'asignaturas_id' => $request->asignatura_id,
                'curso_id' => $cursoId,
            ]);
            $asignaturaCurso->save();
            error_log("saved");
            return response()->json([
                'status' => 'success',
                'message' => 'Asignatura agregada exitosamente',
                'data' => $asignaturaCurso
            ], 200);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al agregar asignatura',
                'data' => $e->getMessage()
            ], 200);
        }
    }
}
