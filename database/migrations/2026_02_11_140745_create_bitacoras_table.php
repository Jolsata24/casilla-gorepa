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
    Schema::create('bitacoras', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained(); // Quién hizo la acción
        $table->string('accion'); // Ej: "LOGIN", "DESCARGA_PDF", "CREAR_USUARIO"
        $table->string('ip_address')->nullable();
        $table->text('detalles')->nullable(); // Ej: "Descargó notificación ID 50"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
