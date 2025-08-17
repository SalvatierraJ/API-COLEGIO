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
        Schema::table('colegios', function (Blueprint $table) {
            $table->foreign(['comuna_id'])->references(['id'])->on('comunas')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['institucion_id'])->references(['id'])->on('instituciones')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colegios', function (Blueprint $table) {
            $table->dropForeign('colegios_comuna_id_foreign');
            $table->dropForeign('colegios_institucion_id_foreign');
        });
    }
};
