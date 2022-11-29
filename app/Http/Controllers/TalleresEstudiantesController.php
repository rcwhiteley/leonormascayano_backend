<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class TalleresEstudiantesController extends Controller
{
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
        error_log($count);
        return $count;
    }
    public function show(Request $request)
    {
        $taller = Taller::with(
            'estudiantes',
            'estudiantes.usuario',
            'estudiantes.evaluacionesTallerRendidas',
            'estudiantes.asistenciaTaller',
            'dias_de_clases'
        )->find($request->id);
        if (!$taller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Taller no encontrado',
                'data' => null
            ], 404);
        }
        $dias_count = $taller->dias_de_clases->count();
        $nuevosEstudiantes = $taller->estudiantes->map(function ($estudiante) use ($dias_count) {
            $usuario = $estudiante->usuario;
            unset($estudiante->usuario);
            $usuario->estudiante = $estudiante;
            $usuario->estudiante->calificaciones_taller = $estudiante->evaluacionesTallerRendidas;
            $usuario->estudiante->promedio = round($usuario->estudiante->calificaciones_taller->avg('calificacion'), 0);
            $usuario->estudiante->porcentaje = round($this->countPresente($usuario->estudiante->asistenciaTaller) * 100 / $dias_count, 1);
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
}
