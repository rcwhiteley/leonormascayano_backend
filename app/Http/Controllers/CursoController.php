<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use App\Models\Curso;
use App\Models\Periodo;
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
                'data' => $e->getMessage()
            ], 200);
        }
    }

    public function getAll()
    {
        $colegios = Colegio::all();
        $cursos = Curso::with('nivel')->get();
        $periodos = Periodo::all();
        foreach ($periodos as $periodo) {
            // unset($periodo->colegios);
            $periodo->colegios = collect([]);
        }

        foreach ($colegios as $colegio) {
            // unset($colegio->cursos);
            foreach ($periodos as $periodo) {
                $colegio->cursos = collect([]);
                $newColegio = clone $colegio;
                $periodo->colegios->push($newColegio);
            }
        }

        foreach ($cursos as $curso) {
            $periodo = $periodos->where('id', $curso->periodos_id)->first();
            $colegio = $periodo->colegios->where('id', $curso->colegio_id)->first();
            $colegio->cursos->push(clone $curso);
            // error_log($colegio->toJson());
        }
        foreach ($periodos as $periodo) {
            $periodo->colegios = $periodo->colegios->where(function ($colegio) {
                return $colegio->cursos->count() > 0;
            })->values();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Cursos obtenidos exitosamente',
            'data' => $periodos
        ], 200);
    }

    public function getCurso(Request $request)
    {
        $id = $request->id;
        $curso = Curso::with('asignaturas_curso', 'asignaturas_curso.asignatura', 'nivel', 'colegio')->find($id);
        if ($curso) {
            return response()->json([
                'status' => 'success',
                'message' => 'Curso obtenido exitosamente',
                'data' => $curso
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Curso no encontrado',
                'data' => null
            ], 200);
        }
    }
}
