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

    public function show(Request $request){
        try {
            $user = Usuario::with('apoderado', 'estudiante', 'tutor', 'administrativo', 'administrador')->find($request->id);
            return response()->json([
                'status' => 'success',
                'message' => 'request completed successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request){
        try {
            $request->validate([
                'primer_nombre' => 'required',
                'segundo_nombre' => 'required',
                'apellido_paterno' => 'required',
                'apellido_materno' => 'required',
                'rut' => 'required',
                'email' => 'required',
            ]);
            error_log(json_encode($request->all()));
            $user = Usuario::with('estudiante', 'tutor', 'apoderado', 'administrativo')->find($request->id);
            $user->primer_nombre = $request->primer_nombre;
            $user->segundo_nombre = $request->segundo_nombre;
            $user->apellido_paterno = $request->apellido_paterno;
            $user->apellido_materno = $request->apellido_materno;
            $user->rut = $request->rut;
            $user->email = $request->email;
            $user->telefono = $request->telefono;
            $user->save();
            if($request->has('estudiante')){
                if($user->estudiante){
                    $user->estudiante->activo = 1;
                    $user->estudiante->save();
                }else{
                    $user->estudiante()->create([
                        'activo' => true,
                        'direccion' => $request->direccion,
                    ]);
                }
            }
            else{
                if($user->estudiante){
                    $user->estudiante->activo = 0;
                    $user->estudiante->save();
                }
            }
            if($request->has('tutor')){
                if($user->tutor){
                    $user->tutor->activo = $request->tutor != null;
                    $user->tutor->save();
                }else{
                    $user->tutor()->create([
                        'activo' => true,
                    ]);
                }
            }
            else{
                if($user->tutor){
                    $user->tutor->activo = 0;
                    $user->tutor->save();
                }
            }
            if($request->has('apoderado')){
                if($user->apoderado){
                    $user->apoderado->activo = $request->apoderado != null;
                    $user->apoderado->save();
                }else{
                    $user->apoderado()->create([
                        'activo' => true,
                        'direccion' => $request->direccion,
                    ]);
                }
            }
            else{
                if($user->apoderado){
                    $user->apoderado->activo = 0;
                    $user->apoderado->save();
                }
            }
            if($request->has('administrativo')){
                if($user->administrativo){
                    $user->administrativo->activo = $request->apoderado != null;
                    $user->administrativo->save();
                }else{
                    $user->administrativo()->create([
                        'activo' => true,
                    ]);
                }
            }
            else{
                if($user->administrativo){
                    $user->administrativo->activo = 0;
                    $user->administrativo->save();
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'request completed successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
