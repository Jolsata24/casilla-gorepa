<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Agregamos el tipo de documento para diferenciar
        $table->enum('tipo_documento', ['DNI', 'RUC'])->default('DNI')->after('email');
        
        // Agregamos RUC y Razón Social (nullables porque un ciudadano no los tiene)
        $table->string('ruc', 11)->nullable()->unique()->after('tipo_documento');
        $table->string('razon_social')->nullable()->after('ruc');

        // Hacemos que los apellidos sean nullables (porque una empresa no tiene apellidos)
        $table->string('apellido_paterno')->nullable()->change();
        $table->string('apellido_materno')->nullable()->change();
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
