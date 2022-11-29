<?php

namespace App\Http\Controllers;

use App\Models\Niveles;
use Illuminate\Http\Request;

class NivelesController extends Controller
{
    public function getAll(){
        $niveles = Niveles::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Niveles obtenidos exitosamente',
            'data' => $niveles
        ], 200);
    }
}
