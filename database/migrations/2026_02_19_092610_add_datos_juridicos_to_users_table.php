<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nuevos campos para Personas Jurídicas
            $table->enum('tipo_documento', ['DNI', 'RUC'])->default('DNI')->after('email');
            $table->string('ruc', 11)->nullable()->unique()->after('tipo_documento');
            $table->string('razon_social')->nullable()->after('ruc');

            // Hacemos que los campos de persona natural sean opcionales
            $table->string('dni', 8)->nullable()->change();
            $table->string('apellido_paterno')->nullable()->change();
            $table->string('apellido_materno')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tipo_documento', 'ruc', 'razon_social']);
            
            // Nota: Revertir change() puede requerir configuración adicional, 
            // pero para desarrollo local esto es suficiente.
        });
    }
};