<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->string('direccion')->nullable()->after('telefono');
            $table->date('fecha_inicio')->nullable()->after('direccion');
        });
    }

    public function down(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->dropColumn(['direccion', 'fecha_inicio']);
        });
    }
};
