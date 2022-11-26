<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Models\Administrador;
use App\Models\Administrativo;
use App\Models\Apoderado;
use App\Models\Estudiante;
use App\Models\Tutor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/hola', function (Request $request) {
    return Usuario::find(1)->with('Tutor')->with('Apoderado')->get();
});

Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/usuarios', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('/usuarios', [UsersController::class, 'index']);


Route::get('/resetUsers', function (Request $request) {
    Administrador::all()->each(function ($item, $key) {
        $item->delete();
    });
    Administrativo::all()->each(function ($item, $key) {
        $item->delete();
    });
    Estudiante::all()->each(function ($item, $key) {
        $item->delete();
    });
    Apoderado::all()->each(function ($item, $key) {
        $item->delete();
    });
    Tutor::all()->each(function ($item, $key) {
        $item->delete();
    });
    Usuario::all()->each(function ($item, $key) {
        $item->delete();
    });
    $admin = new Usuario([
        'primer_nombre' => 'administrador',
        'segundo_nombre' => 'administrador',
        'apellido_paterno' => 'administrador',
        'apellido_materno' => 'administrador',
        'rut' => 'administrador',
        'email' => 'administrador',
        'telefono' => 'administrador',
        'password' => Hash::make('administrador'),
    ]);
    $admin->save();
    $administrador = new Administrador([
        'id' => $admin->id,
        'activo' => 1,
    ]);
    $administrador->save();

    $user1 = new Usuario([
        'primer_nombre' => 'administrativo',
        'segundo_nombre' => 'administrativo',
        'apellido_paterno' => 'administrativo',
        'apellido_materno' => 'administrativo',
        'rut' => 'administrativo',
        'email' => 'administrativo',
        'telefono' => 'administrativo',
        'password' => Hash::make('administrativo'),
    ]);
    $user1->save();
    $administrativo = new Administrativo([
        'id' => $user1->id,
        'activo' => 1,
    ]);
    $administrativo->save();

    for ($i = 0; $i < 10; $i++) {
        $user2 = new Usuario([
            'primer_nombre' => 'apoderado' . $i,
            'segundo_nombre' => 'apoderado' . $i,
            'apellido_paterno' => 'apoderado' . $i,
            'apellido_materno' => 'apoderado' . $i,
            'rut' => 'apoderado' . $i,
            'email' => 'apoderado' . $i,
            'telefono' => 'apoderado' . $i,
            'password' => Hash::make('apoderado' . $i),
        ]);
        $user2->save();
        $apoderado = new Apoderado([
            'id' => $user2->id,
            'activo' => 1,
            'direccion' => 'direccion_apoderado' . $i
        ]);
        $apoderado->save();
    }
    for ($i = 0; $i < 5; $i++) {
        $user3 = new Usuario([
            'primer_nombre' => 'tutor' . $i,
            'segundo_nombre' => 'tutor' . $i,
            'apellido_paterno' => 'tutor' . $i,
            'apellido_materno' => 'tutor' . $i,
            'rut' => 'tutor' . $i,
            'email' => 'tutor' . $i,
            'telefono' => 'tutor' . $i,
            'password' => Hash::make('tutor' . $i),
        ]);
        $user3->save();
        $tutor = new Tutor([
            'id' => $user3->id,
            'activo' => 1,
        ]);
        $tutor->save();
    }

    for ($i = 0; $i < 40; $i++) {
        $user4 = new Usuario([
            'primer_nombre' => 'alumno' . $i,
            'segundo_nombre' => 'alumno' . $i,
            'apellido_paterno' => 'alumno' . $i,
            'apellido_materno' => 'alumno' . $i,
            'rut' => 'alumno' . $i,
            'email' => 'alumno' . $i,
            'telefono' => 'alumno' . $i,
            'password' => Hash::make('alumno' . $i),
        ]);
        $user4->save();
        $estudiante = new Estudiante([
            'id' => $user4->id,
            'activo' => 1,
            'direccion' => 'direccion_alumno' . $i,
        ]);
        $estudiante->save();
    }
    return 'Usuarios creados';
});
