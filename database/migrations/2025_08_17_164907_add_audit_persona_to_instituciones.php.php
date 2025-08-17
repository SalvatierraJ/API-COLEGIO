<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->unsignedBigInteger('registrado_por_persona_id')->nullable()->after('estado');
            $table->unsignedBigInteger('actualizado_por_persona_id')->nullable()->after('registrado_por_persona_id');

            $table->foreign('registrado_por_persona_id', 'fk_inst_reg_por_persona')
                ->references('id')->on('personas')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('actualizado_por_persona_id', 'fk_inst_act_por_persona')
                ->references('id')->on('personas')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->dropForeign('fk_inst_reg_por_persona');
            $table->dropForeign('fk_inst_act_por_persona');
            $table->dropColumn(['registrado_por_persona_id', 'actualizado_por_persona_id']);
        });
    }
};
