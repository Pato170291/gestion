<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/clientes', [ClienteController::class, 'index']);
Route::get('/clientes/crear', [ClienteController::class, 'crear']);
Route::post('/clientes', [ClienteController::class, 'store']);
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit']);
Route::put('/clientes/{id}', [ClienteController::class, 'update']);