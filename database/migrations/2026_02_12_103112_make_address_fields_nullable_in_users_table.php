<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hacemos que estos campos acepten NULL para que no falle el registro inicial
            $table->string('departamento')->nullable()->change();
            $table->string('provincia')->nullable()->change();
            $table->string('distrito')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            
            // Agregamos celular si no existía, o lo hacemos nullable si ya existía
            if (!Schema::hasColumn('users', 'celular')) {
                $table->string('celular')->nullable();
            } else {
                $table->string('celular')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir cambios (volver a no-nulos) es riesgoso si hay data, 
            // así que en el down generalmente lo dejamos o definimos lo inverso con cuidado.
        });
    }
};