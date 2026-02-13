<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla de Etiquetas (Carpetas del usuario)
        Schema::create('etiquetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Dueño de la etiqueta
            $table->string('nombre'); // Ej: "Contratos", "Importante"
            $table->string('color')->default('gray'); // Para ponerle bolitas de colores
            $table->timestamps();
        });

        // 2. Modificar Notificaciones para soportar etiquetas y favoritos
        Schema::table('notificaciones', function (Blueprint $table) {
            // Si es NULL, está en "Recibidos" (Bandeja principal)
            $table->foreignId('etiqueta_id')->nullable()->constrained('etiquetas')->onDelete('set null');
            
            // Para la estrellita de "Importante"
            $table->boolean('es_destacado')->default(false);
            
            // Para la papelera (Soft Deletes) - Opcional pero recomendado estilo Gmail
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['etiqueta_id']);
            $table->dropColumn(['etiqueta_id', 'es_destacado', 'deleted_at']);
        });
        Schema::dropIfExists('etiquetas');
    }
};