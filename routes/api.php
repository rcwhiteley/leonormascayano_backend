<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyCsrfToken;
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
Route::get('/createUsers', function (Request $request) {
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

    $user2 = new Usuario([
        'primer_nombre' => 'apoderado',
        'segundo_nombre' => 'apoderado',
        'apellido_paterno' => 'apoderado',
        'apellido_materno' => 'apoderado',
        'rut' => 'apoderado',
        'email' => 'apoderado',
        'telefono' => 'apoderado',
        'password' => Hash::make('apoderado'),
    ]);
    $user2->save();
    $apoderado = new Apoderado([
        'id' => $user2->id,
        'activo' => 1,
    ]);
    $apoderado->save();
    $user3 = new Usuario([
        'primer_nombre' => 'tutor',
        'segundo_nombre' => 'tutor',
        'apellido_paterno' => 'tutor',
        'apellido_materno' => 'tutor',
        'rut' => 'tutor',
        'email' => 'tutor',
        'telefono' => 'tutor',
        'password' => Hash::make('tutor'),
    ]);
    $user3->save();
    $tutor = new Tutor([
        'id' => $user3->id,
        'activo' => 1,
    ]);

    $user4 = new Usuario([
        'primer_nombre' => 'alumno',
        'segundo_nombre' => 'alumno',
        'apellido_paterno' => 'alumno',
        'apellido_materno' => 'alumno',
        'rut' => 'alumno',
        'email' => 'alumno',
        'telefono' => 'alumno',
        'password' => Hash::make('alumno'),
    ]);
    $user4->save();
    $estudiante = new Estudiante([
        'id' => $user4->id,
        'activo' => 1,
    ]);
    $estudiante->save();

    return 'Usuarios creados';
});
