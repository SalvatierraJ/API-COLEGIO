<?php

use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//Rutas Publicas
Route::post('/register', [UsuarioController::class, 'register']);
Route::post('/login', [UsuarioController::class, 'login']);

//Rutas protegidas
Route::middleware(['auth:api'])->group(function () {
    Route::get('/auth/profile', [UsuarioController::class, 'obtenerPerfil']);
    Route::post('/auth/logout', [UsuarioController::class, 'logout']);

    Route::get('/regiones', [RegionController::class, 'index']);
    Route::get('/regiones/{region}/comunas', [RegionController::class, 'comunas']);

    Route::get('/instituciones',        [InstitucionController::class, 'index']);
    Route::post('/instituciones',        [InstitucionController::class, 'store']);
    Route::get('/instituciones/{id}',    [InstitucionController::class, 'show'])->name('instituciones.show');
    Route::put('/instituciones/{id}',    [InstitucionController::class, 'update']);
});
