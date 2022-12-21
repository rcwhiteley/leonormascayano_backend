<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TalleresEstudiantesController extends Controller
{

    private function calculateAvg($calificaciones, $evaluaciones)
    {
        try {
            $result = 0.0;
            foreach ($evaluaciones as $evaluacion) {
                foreach ($calificaciones as $calificacion) {
                    if ($calificacion->evaluaciones_taller_id == $evaluacion->id) {
                        $porcentaje = (float)$evaluacion->ponderacion / 100;
                        $val = (float)$calificacion->calificacion * (float)$evaluacion->ponderacion / 100;
                        $result += $val;
                    }
                }
            }
            return round($result, 1);
            return $result;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
    private function countPresente($asistencias)
    {
        $count = 0;
        if ($asistencias) {

            foreach ($asistencias as $asistencia) {
                if ($asistencia->asistio === 1) {
                    $count++;
                }
            }
        }
        return $count;
    }
    public function show(Request $request)
    {
        $taller = Taller::with(
            [
                'estudiantes',
                'estudiantes.usuario',
                'estudiantes.evaluacionesTallerRendidas' => function ($query) use ($request) {
                    $query->where('taller_id', $request->id);
                },
                'estudiantes.asistenciaTaller',
                'dias_de_clases',
                'evaluaciones'
            ]
        )->find($request->id);
        if (!$taller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Taller no encontrado',
                'data' => null
            ], 404);
        }
        $dias_count = $taller->dias_de_clases->count();
        $nuevosEstudiantes = $taller->estudiantes->map(function ($estudiante) use ($dias_count, $taller) {
            $usuario = $estudiante->usuario;
            unset($estudiante->usuario);
            $usuario->estudiante = $estudiante;
            $usuario->estudiante->calificaciones_taller = $estudiante->evaluacionesTallerRendidas;
            $usuario->estudiante->promedio = $this->calculateAvg($usuario->estudiante->calificaciones_taller, $taller->evaluaciones);
            $usuario->estudiante->porcentaje = round($this->countPresente($usuario->estudiante->asistenciaTaller) * 100 / max($dias_count, 1), 1);
            unset($usuario->estudiante->evaluacionesTallerRendidas);
            return $usuario;
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Taller encontrado',
            'data' => $nuevosEstudiantes
        ], 200);
    }

    public function addStudent(Request $request)
    {
        try {
            $taller = Taller::find($request->id);
            if ($taller->estudiantes()->where('alumno_id', $request->estudiante_id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El estudiante ya está inscrito en el taller',
                    'data' => null
                ], 400);
            }
            $taller->estudiantes()->attach($request->estudiante_id);
            error_log($request->estudiante_id);
            error_log($request->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Estudiante agregado al taller',
                'data' => $taller
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function notStudents(Request $request)
    {
        $search = '';
        if ($request->has('search')) {
            $search = $request->search;
        }
        error_log($search);
        $users = Usuario::with('estudiante')
            ->whereHas('estudiante', function ($query) {
                $query->where('activo', 1);
            })
            ->where(function ($query) use ($search) {
                $query->where('primer_nombre', 'like', '%' . $search . '%')
                    ->orWhere('segundo_nombre', 'like', '%' . $search . '%')
                    ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
                    ->orWhere('apellido_materno', 'like', '%' . $search . '%')
                    ->orWhere('rut', 'like', '%' . $search . '%');
            })->whereDoesntHave('estudiante.talleres', function ($query) use ($request) {
                $query->where('taller_id', $request->id);
            })->simplePaginate(10);
        return response()->json([
            'status' => 'success',
            'message' => 'Usuarios encontrados',
            'data' => $users
        ], 200);
    }
}
