<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function createCurso(Request $request)
    {
        error_log(json_encode($request->all()));
        //periodoId, nivelId, colegioId, letra
        try {
            $request->validate([
                'colegio_id' => 'required|integer',
                'periodos_id' => 'required|integer',
                'niveles_id' => 'required|integer',
                'letra' => 'required|string',
            ]);
            $curso = new Curso([
                'colegio_id' => $request->colegio_id,
                'periodos_id' => $request->periodos_id,
                'niveles_id' => $request->niveles_id,
                'letra' => $request->letra,
            ]);
            $curso->save();
            error_log("saved");
            return response()->json([
                'status' => 'success',
                'message' => 'Curso creado exitosamente',
                'data' => $curso
            ], 200);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear curso',
                'data' => $e
            ], 500);
        }
    }

    public function getAll(){
        $cursos = Curso::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Cursos obtenidos exitosamente',
            'data' => $cursos
        ], 200);
    }
}
