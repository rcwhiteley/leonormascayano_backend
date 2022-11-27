<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TalleresController extends Controller
{
    function create(Request $request)
    {
        error_log(json_encode($request->all()));
        $request->validate([
            'nombre' => 'required|string',
            'periodos_id' => 'required|integer',
        ]);
        $taller = new Taller([
            'nombre_taller' => $request->nombre,
            'periodos_id' => $request->periodos_id,
            'programa' => 'Contenido del taller',
            'nivel_especializacion_id' => 1,
            'nivel_vinculo_id' => 1,
        ]);
        $taller->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Taller creado exitosamente',
            'data' => $taller
        ], 200);
    }

    function show(Request $request)
    {
        $taller = Taller::with(
            'nivel_vinculo',
            'nivel_especializacion',
            'periodo'
        )->find($request->id);
        return response()->json([
            'status' => 'success',
            'message' => 'Taller encontrado',
            'data' => $taller
        ], 200);
    }

    function index()
    {
        $talleres = Periodo::with(
            'talleres',
            'talleres.nivel_vinculo',
            'talleres.nivel_especializacion',
        )->whereHas('talleres')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Talleres encontrados',
            'data' => $talleres
        ], 200);
    }

    function addStudent(Request $request)
    {
        $taller = Taller::find($request->id);
        $taller->estudiantes()->attach($request->estudiantes_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Estudiante agregado al taller',
            'data' => $taller
        ], 200);
    }
}
