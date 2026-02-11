<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 0 = Pendiente (petición), 1 = Activo, 2 = Rechazado
            // Lo colocamos después de is_admin para mantener el orden de tu tabla
            if (!Schema::hasColumn('users', 'status')) {
                $table->integer('status')->default(0)->after('is_admin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};