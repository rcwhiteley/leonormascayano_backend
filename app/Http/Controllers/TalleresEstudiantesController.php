<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class TalleresEstudiantesController extends Controller
{
    public function show(Request $request)
    {
        $taller = Taller::with(
            'estudiantes',
            'estudiantes.usuario',
        )->find($request->id);
        if (!$taller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Taller no encontrado',
                'data' => null
            ], 404);
        }
        $nuevosEstudiantes = $taller->estudiantes->map(function ($estudiante) {
            $usuario = $estudiante->usuario;
            unset($estudiante->usuario);
            unset($estudiante->pivot);
            $usuario->estudiante = $estudiante;
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
