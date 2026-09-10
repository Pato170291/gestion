<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->decimal('ingresos', 12, 2)->default(0)->after('estado');
            $table->decimal('egresos', 12, 2)->default(0)->after('ingresos');
            $table->decimal('saldo_final', 12, 2)->nullable()->after('egresos');
            $table->decimal('dinero_contado', 12, 2)->nullable()->after('saldo_final');
            $table->decimal('diferencia', 12, 2)->nullable()->after('dinero_contado');
            $table->dateTime('fecha_cierre')->nullable()->after('diferencia');
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn([
                'ingresos',
                'egresos',
                'saldo_final',
                'dinero_contado',
                'diferencia',
                'fecha_cierre',
            ]);
        });
    }
};
