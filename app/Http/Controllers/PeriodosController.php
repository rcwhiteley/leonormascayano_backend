<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeriodosController extends Controller
{
    function index()
    {
        $periodos = Periodo::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Periodos consultado exitosamente',
            'data' => $periodos
        ], 200);
    }

    function create(Request $request)
    {
        error_log(json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
        ]);
        if ($validator->fails()) {
            error_log(json_encode($validator->errors()));
            return response()->json($validator->errors(), 400);
        }

        $periodo = new Periodo([
            'nombre' => $request->nombre,
        ]);
        $periodo->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Periodo creado exitosamente',
            'data' => $periodo
        ], 200);
    }
}
