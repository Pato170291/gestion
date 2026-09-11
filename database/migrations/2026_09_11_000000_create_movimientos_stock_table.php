<?php

use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->unsignedInteger('cantidad');
            $table->string('motivo');
            $table->date('fecha');
            $table->timestamps();
        });

        // Genera el movimiento de entrada inicial para productos ya cargados con stock,
        // para que el módulo de Stock no los muestre en 0 al pasar a calcular por movimientos.
        Producto::where('stock_actual', '>', 0)->whereDoesntHave('movimientosStock')->each(function (Producto $producto) {
            MovimientoStock::create([
                'producto_id' => $producto->id,
                'tipo' => 'entrada',
                'cantidad' => $producto->stock_actual,
                'motivo' => 'Stock inicial',
                'fecha' => $producto->created_at?->toDateString() ?? now()->toDateString(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
