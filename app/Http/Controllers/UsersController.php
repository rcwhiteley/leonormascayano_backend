<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        try {
            $users =  Usuario::with('apoderado', 'estudiante', 'tutor', 'administrativo', 'administrador')->simplePaginate(10);
            return response()->json([
                'status' => 'success',
                'message' => 'request completed successfully',
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchEstudiantes(Request $request)
    {
        try {
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
                })->simplePaginate(10);
            return response()->json([
                'status' => 'success',
                'message' => 'request completed successfully',
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
