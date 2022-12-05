<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\EvaluacionesTaller;
use App\Models\EvaluacionesTallerRendidas;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TallerCalificacionesController extends Controller
{
    private function createEvaluacion($taller_id, $nombre)
    {
        $evaluacion = new EvaluacionesTaller([
            'taller_id' => $taller_id,
            'nombre' => $nombre,
            'descripcion' => ''
        ]);
        $evaluacion->save();
        return $evaluacion;
    }

    private function findEstudiante($estudiantes, $estudiante_id)
    {
        foreach ($estudiantes as $estudiante) {
            if ($estudiante->id == $estudiante_id) {
                return $estudiante;
            }
        }
        return null;
    }

    public function add(Request $request)
    {
        try {
            $taller = Taller::with('estudiantes')->find($request->id);
            if (!$taller) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Taller no encontrado',
                    'data' => null
                ], 404);
            }
            $data = $request->all();
            $nombre = $data['nombre'];

            $estudiantes = $taller->estudiantes;
            DB::beginTransaction();
            $evaluacion = $this->createEvaluacion($request->id, $nombre);
            $evaluacionId = $evaluacion->id;
            $calificaciones = $data['calificaciones'];
            foreach ($calificaciones as $estudiante_id => $calificacion) {
                $estudiante = $this->findEstudiante($estudiantes, $estudiante_id);
                if ($estudiante == null) {
                    continue;
                }
                $tallerHasAlumno_id = $estudiante->pivot->id;
                $evaluacion = new EvaluacionesTallerRendidas([
                    'evaluaciones_taller_id' => $evaluacionId,
                    'calificacion' => $calificacion,
                    'taller_has_alumno_id' => $tallerHasAlumno_id
                ]);
                $evaluacion->save();
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Calificacioens agregadas al taller',
                'data' => $estudiantes
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAll(Request $request)
    {
        $taller = Taller::with(
            'evaluaciones',
        )->find($request->id);
        if (!$taller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Taller no encontrado',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Taller encontrado',
            'data' => $taller['evaluaciones']
        ], 200);
    }

    public function addEvaluacion(Request $request)
    {
        try {
            $taller = Taller::find($request->id);
            if (!$taller) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Taller no encontrado',
                    'data' => null
                ], 404);
            }
            error_log(json_encode($request->all()));
            $validated = $request->validate([
                'nombre' => 'required|string',
                'ponderacion' => 'required|integer',
                'descripcion' => 'required|string',
                'fecha' => 'required|date'
            ]);

            $evaluacion = new EvaluacionesTaller([
                'taller_id' => $request->id,
                'nombre' => $validated['nombre'],
                'ponderacion' => $validated['ponderacion'],
                'descripcion' => $validated['descripcion'],
                'fecha' => $validated['fecha'],
            ]);
            $evaluacion->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Evaluacion agregada al taller',
                'data' => $evaluacion
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateEvaluacion(Request $request)
    {
        try {
            $taller = Taller::find($request->id);
            if (!$taller) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Taller no encontrado',
                    'data' => null
                ], 404);
            }
            $evaluacion = EvaluacionesTaller::find($request->evaluacionid);
            if (!$evaluacion) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Evaluacion no encontrada',
                    'data' => null
                ], 404);
            }
            $validated = $request->validate([
                'nombre' => 'required|string',
                'ponderacion' => 'required|integer',
                'descripcion' => 'required|string',
                'fecha' => 'required|date'
            ]);

            $evaluacion->nombre = $validated['nombre'];
            $evaluacion->ponderacion = $validated['ponderacion'];
            $evaluacion->descripcion = $validated['descripcion'];
            $evaluacion->fecha = $validated['fecha'];
            $evaluacion->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Evaluacion actualizada',
                'data' => $evaluacion
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateCalificaciones(Request $request)
    {
        try {
            $taller = Taller::with('estudiantes')->find($request->id);
            if (!$taller) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Taller no encontrado',
                    'data' => null
                ], 404);
            }
            $evaluacion = EvaluacionesTaller::find($request->evaluacionid);
            if (!$evaluacion) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Evaluacion no encontrada',
                    'data' => null
                ], 404);
            }
            $validated = $request->validate([
                'calificaciones' => 'required|array'
            ]);
            error_log("aaaaaaaaaaaaaaaaah");
            DB::beginTransaction();
            //EvaluacionesTallerRendidas::where('evaluaciones_taller_id', $evaluacion->id)->delete();

            $data = $request->all();
            $calificaciones = $data['calificaciones'];
            $estudiantes = $taller->estudiantes;
            foreach ($calificaciones as $estudiante_id => $calificacion) {
                $estudiante = $this->findEstudiante($estudiantes, $estudiante_id);
                if ($estudiante == null) {
                    continue;
                }
                $tallerHasAlumno_id = $estudiante->pivot->id;
                $evaluacionRendida = EvaluacionesTallerRendidas::where('evaluaciones_taller_id', $evaluacion->id)
                    ->where('taller_has_alumno_id', $tallerHasAlumno_id)->first();
                if (!$evaluacionRendida) {
                    $evaluacionRendida = new EvaluacionesTallerRendidas([
                        'evaluaciones_taller_id' => $evaluacion->id,
                        'calificacion' => $calificacion,
                        'taller_has_alumno_id' => $tallerHasAlumno_id
                    ]);
                } else {
                    $evaluacionRendida->calificacion = $calificacion;
                }
                $evaluacionRendida->save();
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Calificaciones actualizadas',
                'data' => $evaluacion
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
