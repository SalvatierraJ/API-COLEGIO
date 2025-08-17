<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_educativo', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_educativo', 'colegio_id')) {
                $table->unsignedBigInteger('colegio_id')->after('persona_id');
                $table->foreign('colegio_id')
                    ->references('id')
                    ->on('colegios')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
            }

            if (!Schema::hasColumn('personal_educativo', 'persona_id')) {
            }
        });

        Schema::table('personal_educativo', function (Blueprint $table) {
            $table->unique('persona_id', 'personal_educativo_persona_id_unique');
            $table->index('colegio_id', 'personal_educativo_colegio_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('personal_educativo', function (Blueprint $table) {
            $table->dropUnique('personal_educativo_persona_id_unique');
            $table->dropIndex('personal_educativo_colegio_id_index');
            $table->dropForeign(['colegio_id']);
            $table->dropColumn('colegio_id');
        });
    }
};
