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
        Schema::table('colegio_personal', function (Blueprint $table) {
            $table->foreign(['colegio_id'])->references(['id'])->on('colegios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['personal_educativo_id'])->references(['id'])->on('personal_educativo')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colegio_personal', function (Blueprint $table) {
            $table->dropForeign('colegio_personal_colegio_id_foreign');
            $table->dropForeign('colegio_personal_personal_educativo_id_foreign');
        });
    }
};
