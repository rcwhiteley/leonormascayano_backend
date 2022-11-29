<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use Illuminate\Http\Request;

class ColegiosController extends Controller
{
    public function getAll(){
        $colegios = Colegio::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Colegios obtenidos exitosamente',
            'data' => $colegios
        ], 200);
    }
}
