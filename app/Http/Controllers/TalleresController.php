<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use Illuminate\Http\Request;

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
            'nivel_especializacion_id'=> 1,
            'nivel_vinculo_id'=> 1,
        ]);
        $taller->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Taller creado exitosamente',
            'data' => $taller
        ], 200);
    }
}
