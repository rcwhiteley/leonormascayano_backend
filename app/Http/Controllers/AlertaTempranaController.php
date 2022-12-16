<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class AlertaTempranaController extends Controller
{
    public function getAlertas(Request $request){
        $alertas = Periodo::with(
            'curso',
            'curso.niveles',
            'curso.colegio',
            
        )->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Alertas obtenidas exitosamente',
            'data' => $alertas
        ], 200);
    }
}
