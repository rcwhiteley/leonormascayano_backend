<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Http\Request;

class CursoEstudiantesController extends Controller
{
    public function notStudents(Request $request)
    {
        $search = '';
        if ($request->has('search')) {
            $search = $request->search;
        }
        error_log($search);
        $users = Usuario::with('estudiante')
        ->whereHas('estudiante')
        ->where(function ($query) use ($search) {
            $query->where('primer_nombre', 'like', '%' . $search . '%')
                ->orWhere('segundo_nombre', 'like', '%' . $search . '%')
                ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
                ->orWhere('apellido_materno', 'like', '%' . $search . '%')
                ->orWhere('rut', 'like', '%' . $search . '%');
        })->whereDoesntHave('estudiante.cursos', function ($query) use ($request) {
            $query->where('curso_id', $request->id);
        })->simplePaginate(10);
        return response()->json([
            'status' => 'success',
            'message' => 'Usuarios encontrados',
            'data' => $users
        ], 200);
    }

    public function addStudent(Request $request)
    {
        try {
            $curso = Curso::find($request->id);
            if($curso == null) {
               error_log('Curso no encontrado '.$request->id );
            }
            if ($curso->estudiantes()->where('alumno_id', $request->estudiante_id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El estudiante ya está inscrito en el curso',
                    'data' => null
                ], 400);
            }
            $curso->estudiantes()->attach($request->estudiante_id);
            error_log($request->estudiante_id);
            error_log($request->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Estudiante agregado al curso',
                'data' => $curso
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        $curso = Curso::with(
            [
                'estudiantes',
                'estudiantes.usuario',
            ]
        )->find($request->id);
        if (!$curso) {
            return response()->json([
                'status' => 'error',
                'message' => 'Taller no encontrado',
                'data' => null
            ], 404);
        }
        $nuevosEstudiantes = $curso->estudiantes->map(function ($estudiante)  {
            $usuario = $estudiante->usuario;
            unset($estudiante->usuario);
            $usuario->estudiante = $estudiante;
            //$usuario->estudiante->porcentaje = round($this->countPresente($usuario->estudiante->asistenciaClases) * 100 / max($dias_count, 1), 1);
            return $usuario;
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Taller encontrado',
            'data' => $nuevosEstudiantes
        ], 200);
    }
}
