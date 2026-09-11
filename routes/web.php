<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return view('dashboard');
});

// Rutas para clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/crear', [ClienteController::class, 'crear'])->name('clientes.crear');
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::post('/clientes/{id}/estado', [ClienteController::class, 'cambiarEstado'])->name('clientes.estado');
Route::get('/clientes/{id}/cuenta-corriente', [ClienteController::class, 'cuentaCorriente'])->name('clientes.cuenta-corriente');
Route::post('/clientes/{id}/cuenta-corriente/pagos', [ClienteController::class, 'registrarPago'])->name('clientes.cuenta-corriente.pagos');
Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');

// Rutas para proveedores
Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::get('/proveedores/crear', [ProveedorController::class, 'crear'])->name('proveedores.crear');
Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
Route::post('/proveedores/{id}/estado', [ProveedorController::class, 'cambiarEstado'])->name('proveedores.estado');
Route::get('/proveedores/{id}/cuenta-corriente', [ProveedorController::class, 'cuentaCorriente'])->name('proveedores.cuenta-corriente');
Route::post('/proveedores/{id}/cuenta-corriente/pagos', [ProveedorController::class, 'registrarPago'])->name('proveedores.cuenta-corriente.pagos');
Route::get('/proveedores/{id}/editar', [ProveedorController::class, 'edit'])->name('proveedores.edit');
Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');

// Rutas para productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'crear'])->name('productos.crear');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::post('/productos/{id}/estado', [ProductoController::class, 'cambiarEstado'])->name('productos.estado');
Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');

// Rutas para ventas

Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/ventas/crear', [VentaController::class, 'crear'])->name('ventas.crear');
Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
Route::get('/ventas/{id}/editar', [VentaController::class, 'edit'])->name('ventas.edit');
Route::put('/ventas/{id}', [VentaController::class, 'update'])->name('ventas.update');
Route::post('/ventas/{id}/anular', [VentaController::class, 'anular'])->name('ventas.anular');

// Rutas para compras

Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
Route::get('/compras/crear', [CompraController::class, 'crear'])->name('compras.crear');
Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
Route::get('/compras/{id}', [CompraController::class, 'show'])->name('compras.show');
Route::get('/compras/{id}/editar', [CompraController::class, 'edit'])->name('compras.edit');
Route::put('/compras/{id}', [CompraController::class, 'update'])->name('compras.update');
Route::post('/compras/{id}/anular', [CompraController::class, 'anular'])->name('compras.anular');

// Rutas para caja

Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');
Route::post('/caja/movimientos', [CajaController::class, 'guardarMovimiento'])->name('caja.movimientos.store');

//Rutas para stock

Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
Route::post('/stock/movimientos', [StockController::class, 'guardarMovimiento'])->name('stock.movimientos.store');

Route::get('/reportes', function () {
    return view('reportes');
});

