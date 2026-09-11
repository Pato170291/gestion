<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->foreignId('compra_id')->nullable()->after('venta_anulada_id')->constrained('compras')->nullOnDelete();
            $table->foreignId('compra_anulada_id')->nullable()->after('compra_id')->constrained('compras')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->dropForeign(['compra_id']);
            $table->dropForeign(['compra_anulada_id']);
            $table->dropColumn(['compra_id', 'compra_anulada_id']);
        });
    }
};