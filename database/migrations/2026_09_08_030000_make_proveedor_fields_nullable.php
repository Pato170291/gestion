<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('contacto')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('cuit')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('contacto')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('direccion')->nullable(false)->change();
            $table->string('cuit')->nullable(false)->change();
        });
    }
};