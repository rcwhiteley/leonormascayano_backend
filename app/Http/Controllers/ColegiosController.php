<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use App\Models\Periodo;
use Illuminate\Http\Request;

class ColegiosController extends Controller
{
    public function getAll()
    {
        $colegios = Colegio::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Colegios obtenidos exitosamente',
            'data' => $colegios
        ], 200);
    }

    public function createColegio(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'direccion' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'director' => 'required'
            ]);
            $colegio = new Colegio();
            $colegio->nombre = $request->nombre;
            $colegio->direccion = $request->direccion;
            $colegio->celular = $request->telefono;
            $colegio->email = $request->email;
            $colegio->nombre_director = $request->director;
            $colegio->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Colegio agregado exitosamente',
                'data' => $colegio
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al agregar el colegio',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function getCursosColegio(Request $request)
    {
        try {
            $colegio_id = $request->id;
            $colegio = Colegio::with('cursos')->where('id', $colegio_id)->first();
            $periodosIds = $colegio->cursos->map(function ($curso) {
                return $curso->periodos_id;
            })->unique();
            $periodos = Periodo::whereIn('id', $periodosIds)->get();
            error_log($colegio->toJson());
            foreach ($periodos as $periodo) {
                $periodo->cursos = collect([]);
            }
            foreach ($colegio->cursos as $curso) {
                error_log($curso->toJson());
                foreach ($periodos as $periodo) {
                    if ($periodo->id == $curso->periodos_id) {
                        $periodo->cursos->push($curso);
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cursos obtenidos exitosamente',
                'data' => $periodos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener cursos',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
