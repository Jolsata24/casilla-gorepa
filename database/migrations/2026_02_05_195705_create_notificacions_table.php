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
    Schema::create('notificacions', function (Blueprint $table) {
        $table->id();
        
        // Relación: A qué ciudadano le pertenece esta notificación
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->string('asunto');            // Ej: "Resolución N° 123-2026-GOREPA"
        $table->text('mensaje')->nullable(); // Mensaje corto
        
        // SEGURIDAD: Guardaremos la ruta interna, no la pública
        $table->string('ruta_archivo_pdf'); 
        
        // LEGAL: Si este campo está vacío, no lo ha leído.
        $table->timestamp('fecha_lectura')->nullable();
        
        // AUDITORÍA: IP desde donde leyó el documento
        $table->string('ip_lectura', 45)->nullable();
        
        $table->timestamps(); // Crea fecha_envio (created_at)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};
