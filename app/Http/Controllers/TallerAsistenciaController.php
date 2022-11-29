<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaATaller;
use App\Models\DiasDeClases;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TallerAsistenciaController extends Controller
{
    private function getAsistio($asistencias, $estudiante_id)
    {
        foreach ($asistencias as $user_id => $asistio) {
            if ($user_id == $estudiante_id) {
                return $asistio;
            }
        }
        return false;
    }

    public function getAll(Request $request){
        $taller = Taller::with('dias_de_clases')->find($request->id);
        if (!$taller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Taller no encontrado',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Asistencias obtenidas',
            'data' => $taller['dias_de_clases']
        ], 200);
    }

    public function add(Request $request){
        try{
            $taller = Taller::with('estudiantes')->find($request->id);
            $data = $request->all();
            $estudiantes = $taller->estudiantes;

            DB::beginTransaction();
            $diaDeClases = new DiasDeClases([
                'taller_idTaller' => $request->id,
                'fecha' => $data['fecha'],
            ]);
            $diaDeClases->save();
            $diaDeClasesId = $diaDeClases->id;
            foreach($estudiantes as $estudiante){
                $asistio = $this->getAsistio($data['asistencia'], $estudiante->id);
                $tallerHasAlumnoId = $estudiante->pivot->id;
                $asistenciaATaller = new AsistenciaATaller([
                    'dias_de_clases_id' => $diaDeClasesId,
                    'taller_has_alumno_id' => $tallerHasAlumnoId,
                    'asistio' => $asistio
                ]);
                $asistenciaATaller->save();
                error_log('insertado '. $asistenciaATaller->id);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Asistencia registrada',
                'data' => null
            ], 200);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
