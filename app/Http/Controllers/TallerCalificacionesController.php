<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\EvaluacionesTaller;
use App\Models\EvaluacionesTallerRendidas;
use App\Models\Taller;
use App\Models\TallerHasAlumno;
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
        // this should be a transaction
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
            $newData = [];
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
            // error_log(json_encode($data));

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
}
