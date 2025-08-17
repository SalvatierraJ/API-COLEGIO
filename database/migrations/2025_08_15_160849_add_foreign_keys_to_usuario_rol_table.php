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
        Schema::table('usuario_rol', function (Blueprint $table) {
            $table->foreign(['rol_id'])->references(['id'])->on('roles')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['usuario_id'])->references(['id'])->on('usuarios')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario_rol', function (Blueprint $table) {
            $table->dropForeign('usuario_rol_rol_id_foreign');
            $table->dropForeign('usuario_rol_usuario_id_foreign');
        });
    }
};
