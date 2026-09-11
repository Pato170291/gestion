<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->foreignId('venta_id')->nullable()->after('caja_id')->constrained('ventas')->nullOnDelete();
            $table->foreignId('venta_anulada_id')->nullable()->after('venta_id')->constrained('ventas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropForeign(['venta_anulada_id']);
            $table->dropColumn(['venta_id', 'venta_anulada_id']);
        });
    }
};