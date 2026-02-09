<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Añadimos las columnas faltantes después del campo 'name'
        $table->string('apellido_paterno')->nullable()->after('name');
        $table->string('apellido_materno')->nullable()->after('apellido_paterno');
        $table->string('departamento')->nullable()->after('apellido_materno');
        $table->string('provincia')->nullable()->after('departamento');
        $table->string('distrito')->nullable()->after('provincia');
        $table->string('direccion')->nullable()->after('distrito');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
