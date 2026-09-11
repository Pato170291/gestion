<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierres_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->unique()->constrained('cajas')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('saldo_inicial', 12, 2);
            $table->decimal('total_ingresos', 12, 2);
            $table->decimal('total_egresos', 12, 2);
            $table->decimal('saldo_esperado', 12, 2);
            $table->decimal('dinero_contado', 12, 2);
            $table->decimal('diferencia', 12, 2);
            $table->string('estado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_caja');
    }
};