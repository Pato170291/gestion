<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;

Route::get('/', function () {
    return view('dashboard');
});

// Rutas para clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/crear', [ClienteController::class, 'crear'])->name('clientes.crear');
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');

// Rutas para proveedores
Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::get('/proveedores/crear', [ProveedorController::class, 'crear'])->name('proveedores.crear');
Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
Route::get('/proveedores/{id}/editar', [ProveedorController::class, 'edit'])->name('proveedores.edit');
Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

// Rutas para productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'crear'])->name('productos.crear');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');

// Rutas para ventas

Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/ventas/crear', [VentaController::class, 'crear'])->name('ventas.crear');
Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
Route::get('/ventas/{id}/editar', [VentaController::class, 'edit'])->name('ventas.edit');
Route::put('/ventas/{id}', [VentaController::class, 'update'])->name('ventas.update');
Route::delete('/ventas/{id}', [VentaController::class, 'destroy'])->name('ventas.destroy');

Route::get('/compras', function () {
    return view('compras');
});

Route::get('/caja', function () {
    return view('caja');
});

Route::get('/stock', function () {
    return view('stock');
});

Route::get('/reportes', function () {
    return view('reportes');
});