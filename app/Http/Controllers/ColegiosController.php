<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use Illuminate\Http\Request;

class ColegiosController extends Controller
{
    public function getAll()
    {
        $colegios = Colegio::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Colegios obtenidos exitosamente',
            'data' => $colegios
        ], 200);
    }

    public function createColegio(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'direccion' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'director' => 'required'
            ]);
            $colegio = new Colegio();
            $colegio->nombre = $request->nombre;
            $colegio->direccion = $request->direccion;
            $colegio->celular = $request->telefono;
            $colegio->email = $request->email;
            $colegio->nombre_director = $request->director;
            $colegio->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Colegio agregado exitosamente',
                'data' => $colegio
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al agregar el colegio',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
