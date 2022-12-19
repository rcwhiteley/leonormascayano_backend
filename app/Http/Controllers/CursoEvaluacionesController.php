<?php

namespace App\Http\Controllers;

use App\Models\AsignaturaCurso;
use App\Models\Asignaturas;
use App\Models\Curso;
use App\Models\Evaluaciones;
use App\Models\EvaluacionesRendidas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CursoEvaluacionesController extends Controller
{
    private function findEstudiante($estudiantes, $estudiante_id)
    {
        foreach ($estudiantes as $estudiante) {
            if ($estudiante->id == $estudiante_id) {
                return $estudiante;
            }
        }
        return null;
    }

    public function getAll(Request $request)
    {
        $asignaturaId = $request->asignatura_id;
        $evaluaciones = AsignaturaCurso::with(
            'evaluaciones',
            'evaluaciones.evaluaciones_rendidas',
            'evaluaciones.evaluaciones_rendidas.curso_has_alumnos',
        )->where('id', $asignaturaId)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Evaluaciones obtenidas exitosamente',
            'data' => $evaluaciones
        ], 200);
    }

    public function addEvaluacion(Request $request)
    {
        try {
            error_log(json_encode($request->all()));
            $request->validate([
                'asignatura_id' => 'required|integer',
                'nombre' => 'required|string',
                'ponderacion' => 'required|integer',
                'fecha' => 'required|date',
            ]);
            $evaluacion = new Evaluaciones([
                'nombre' => $request->nombre,
                'ponderacion' => $request->ponderacion,
                'asignatura_curso_id' => $request->asignatura_id,
                'fecha' => $request->fecha,
                'promedio_grupo_control' => $request->promedio_grupo_control ?? 0,
            ]);
            $evaluacion->save();
            error_log("saved");
            return response()->json([
                'status' => 'success',
                'message' => 'Evaluacion agregada exitosamente',
                'data' => $evaluacion
            ], 200);
        } catch (\Exception $e) {
            // error_log($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al agregar evaluacion',
                'data' => $e->getMessage()
            ], 200);
        }
    }

    public function setCalificaciones(Request $request)
    {
        try {
            $curso = Curso::with('estudiantes')->find($request->id);
            if (!$curso) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Curso no encontrado',
                    'data' => null
                ], 404);
            }
            //validate request
            $request->validate([
                'calificaciones' => 'required|array',
            ]);
            $cursoId = $request->id;
            $evaluacionId = $request->evaluacion_id;
            $asignaturaId = $request->asignatura_id;
            $calificaciones = $request->calificaciones;
            $estudiantes = $curso->estudiantes;
            DB::beginTransaction();
            foreach ($calificaciones as $estudiante_id => $calificacion) {
                $estudiante = $this->findEstudiante($estudiantes, $estudiante_id);
                if ($estudiante == null) {
                    continue;
                }
                $evaluacionRendida = EvaluacionesRendidas::where('evaluaciones_Id', $evaluacionId)->where('curso_has_alumno_id', $estudiante->pivot->id)->first();
                if ($evaluacionRendida) {
                    $evaluacionRendida->nota = $calificacion;
                    $evaluacionRendida->save();
                    continue;
                }

                EvaluacionesRendidas::create([
                    'evaluaciones_Id' => $evaluacionId,
                    'curso_has_alumno_id' => $estudiante->pivot->id,
                    'nota' => $calificacion
                ]);
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Calificaciones agregadas a la asignatura',
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
