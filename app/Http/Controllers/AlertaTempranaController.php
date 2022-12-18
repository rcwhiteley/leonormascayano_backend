<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class AlertaTempranaController extends Controller
{
    public function calculatePromedioAsignatura($estudiante, $asignatura)
    {
        // error_log(json_encode($asignatura));
        $evaluaciones = $asignatura->evaluaciones;
        $promedio = 0;
        $total = 0;
        $porcentajeTotal = 0;
        $ultimaEvaluacion = 0;
        $ultimaEvaluacionPonderacion = 0;
        $ultimoPromedio = 0;
        foreach ($evaluaciones as $evaluacion) {
            if ($evaluacion->evaluaciones_rendidas->count() == 0) {
                continue;
            }
            $evaluacion_rendida = $evaluacion->evaluaciones_rendidas->where('curso_has_alumno_id', $estudiante->pivot->id)->first();
            if ($evaluacion_rendida && $evaluacion_rendida->nota > 0) {
                $promedio += $evaluacion_rendida->nota / 100 * $evaluacion->ponderacion;
                $total++;
                $porcentajeTotal += $evaluacion->ponderacion;
                $ultimaEvaluacion = $evaluacion_rendida->nota / 100 * $evaluacion->ponderacion;
                $ultimaEvaluacionPonderacion = $evaluacion->ponderacion;
                // error_log(json_encode($promedio));
            }
        }

        if ($total > 1) {
            $ultimoPromedio = $promedio - $ultimaEvaluacion;
            $ultimoPromedio = $ultimoPromedio * 100 / ($porcentajeTotal - $ultimaEvaluacionPonderacion);
        }

        if ($porcentajeTotal < 100 && $porcentajeTotal > 0) {
            $promedio = $promedio * 100 / $porcentajeTotal;
        }
        $result = new \stdClass();
        $result->ultimo_promedio = $ultimoPromedio;
        $result->promedio = $promedio;
        return $result;
    }

    public function calculatePromedioAlumno($estudiante, $curso)
    {
        $promedio = 0;
        $ultimoPromedio = 0;
        $cantidadPromedios = 0;
        $cantidadUltimosPromedios = 0;
        foreach ($curso->asignaturas_curso as $asignatura) {
            $promedios = $this->calculatePromedioAsignatura($estudiante, $asignatura);
            if ($promedios->promedio > 0) {
                $promedio += $promedios->promedio;
                $cantidadPromedios++;
            }
            if ($promedios->ultimo_promedio > 0) {
                $ultimoPromedio += $promedios->ultimo_promedio;
                $cantidadUltimosPromedios++;
            }
            $estudiante->promedio = round($promedio / $cantidadPromedios);
            if ($cantidadUltimosPromedios > 0)
                $estudiante->ultimo_promedio = $ultimoPromedio / $cantidadUltimosPromedios;
            else
                $estudiante->ultimo_promedio = $estudiante->promedio;
        }
    }

    public function calculatePromedios($curso)
    {
        error_log(json_encode($curso));
        foreach ($curso->estudiantes as $estudiante) {
            $estudiante->ultimo_promedio = 0;
            $estudiante->promedio = 0;
            $this->calculatePromedioAlumno($estudiante, $curso);
        }
        unset($curso->asignaturas_curso);
    }

    public function calculateLatestAsistenciaAlumno($estudiante, $curso)
    {
        $asistenciaActual = 0;
        $ultimaAsistencia = 0;
        // error_log(json_encode(array_keys($curso->fechaRegistroAsistencia)));
        foreach ($curso->fechaRegistroAsistencia as $fechaRegistroAsistencia) {
            error_log(json_encode($fechaRegistroAsistencia));
            $asistencias = $fechaRegistroAsistencia->asistencia_clases->where('curso_has_alumno_id', $estudiante->pivot->id);

            foreach ($asistencias as $asistencia) {
                $ultimaAsistencia = $asistenciaActual;
                $asistenciaActual = $asistencia->porcentaje;
            }
        }
        $estudiante->asistencia = $asistenciaActual;
        $estudiante->ultima_asistencia = $ultimaAsistencia;
    }

    public function calculateLatestAsistencias($curso)
    {
        foreach ($curso->estudiantes as $estudiante) {
            $this->calculateLatestAsistenciaAlumno($estudiante, $curso);
        }
        unset($curso->fechaRegistroAsistencia);
    }

    public function getAll(Request $request)
    {
        $alertas = Periodo::with(
            'cursos',
            'cursos.nivel',
            'cursos.colegio',
            'cursos.asignaturas_curso',
            'cursos.asignaturas_curso.asignatura',
            'cursos.estudiantes',
            'cursos.estudiantes.usuario',
            'cursos.asignaturas_curso.evaluaciones',
            'cursos.asignaturas_curso.evaluaciones.evaluaciones_rendidas',
            'cursos.fechaRegistroAsistencia.asistencia_clases'
        )->whereHas('cursos')->get();

        foreach ($alertas[0]->cursos as $curso) {
            $this->calculatePromedios($curso);
            $this->calculateLatestAsistencias($curso);
        }
        $colegios = [];
        foreach ($alertas[0]->cursos as $curso) {
            if (!isset($colegios[$curso->colegio->id])) {
                $colegios[$curso->colegio->id] = clone $curso->colegio;
                $colegios[$curso->colegio->id]->cursos = [];
            }
        }
        $colegios = collect($colegios);
        $colegios = $colegios->map(function ($colegio) use ($alertas) {
            $colegio->cursos = $alertas[0]->cursos->where('colegio_id', $colegio->id)->map(function ($curso) {
                unset($curso->colegio);
                return $curso;
            });
            $colegio->cursos = $colegio->cursos->values();
            return $colegio;
        });
        // $colegios = $alertas[0]->cursos->groupBy('colegio_id');


        return response()->json([
            'status' => 'success',
            'message' => 'Alertas obtenidas exitosamente',
            'data' => $colegios->values()
        ], 200);
    }

    public function getDetailsStudent(Request $request)
    {
        $detalles = Periodo::with(
            [
                'cursos',
                'cursos.nivel',
                'cursos.colegio',
                'cursos.asignaturas_curso',
                'cursos.asignaturas_curso.asignatura',
                'cursos.estudiantes' => function ($query) use ($request) {
                    $query->where('curso_has_alumno.alumno_id', $request->estudiante_id);
                },
                'cursos.estudiantes.usuario',
                'cursos.asignaturas_curso.evaluaciones',
                'cursos.asignaturas_curso.evaluaciones.evaluaciones_rendidas',
                'cursos.fechaRegistroAsistencia.asistencia_clases'
            ]
        )->whereHas('cursos')->get();
        $detalles[0]->estudiante = $detalles[0]->cursos[0]->estudiantes[0];
        $detalles[0]->curso = $detalles[0]->cursos[0];
        foreach ($detalles[0]->curso->asignaturas_curso as $asignatura) {
            error_log(json_encode($detalles[0]->estudiante->pivot->id));
            foreach ($asignatura->evaluaciones as $evaluacion) {
                $evaluacion->evaluacion_rendida = $evaluacion->evaluaciones_rendidas->where('curso_has_alumno_id', $detalles[0]->estudiante->pivot->id)->first();
                unset($evaluacion->evaluaciones_rendidas);
            }
        }
        $asistencias = [];
        foreach ($detalles[0]->curso->fechaRegistroAsistencia as $fechaRegistroAsistencia) {
            error_log(json_encode($fechaRegistroAsistencia));
            $asistencia = $fechaRegistroAsistencia->asistencia_clases->where('curso_has_alumno_id', $detalles[0]->estudiante->pivot->id)->first();
            $asistencia->fecha = $fechaRegistroAsistencia->fecha;
            array_push($asistencias, $asistencia);
        }
        unset($detalles[0]->curso->fechaRegistroAsistencia);

        unset($detalles[0]->curso->estudiantes);
        $detalles[0]->asistencias = $asistencias;
        unset($detalles[0]->cursos);

        return response()->json([
            'status' => 'success',
            'message' => 'Alertas obtenidas exitosamente',
            'data' => $detalles[0]
        ], 200);
        // $colegios = $alertas[0]->cursos->groupBy('colegio_id');
    }
}
