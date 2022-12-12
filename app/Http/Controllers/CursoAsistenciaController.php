<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaClases;
use App\Models\Curso;
use App\Models\FechaRegistroAsistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CursoAsistenciaController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $curso = Curso::with('fechaRegistroAsistencia', 'fechaRegistroAsistencia.asistencia_clases', 'fechaRegistroAsistencia.asistencia_clases.estudiante_pivot')->find($request->id);
            if (!$curso) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'curso no encontrado',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Asistencias obtenidas',
                'data' => $curso
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 200);
        }
    }

    public function add(Request $request)
    {
        try {
            $curso = Curso::with('estudiantes')->find($request->id);
            if (!$curso) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'curso no encontrado',
                    'data' => null
                ], 404);
            }
            DB::beginTransaction();
            $fechaRegistroAsistencia = FechaRegistroAsistencia::create([
                'fecha' => $request->fecha,
                'curso_id' => $request->id
            ]);
            foreach ($curso->estudiantes as $estudiante) {
                error_log($estudiante);
                AsistenciaClases::create([
                    'curso_has_alumno_id' => $estudiante->pivot->id,
                    'fecha_registro_asistencia_id' => $fechaRegistroAsistencia->id,
                    'porcentaje' => $request->asistencia[$estudiante->id]
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Asistencia agregada',
                'data' => $curso
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al agregar asistencia',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
