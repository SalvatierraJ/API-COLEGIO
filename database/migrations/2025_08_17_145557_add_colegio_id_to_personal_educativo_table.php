<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM personal_educativo'));
        $hasPersonaUnique = $indexes->first(fn ($i) => $i->Key_name === 'personal_educativo_persona_id_unique') !== null;
        $hasColegioIndex  = $indexes->first(fn ($i) => $i->Key_name === 'personal_educativo_colegio_id_index') !== null;

        $fkName = 'personal_educativo_colegio_id_foreign';
        $fkExists = (int) DB::selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND TABLE_NAME = 'personal_educativo'
              AND CONSTRAINT_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$fkName])->c > 0;

        if (!Schema::hasColumn('personal_educativo', 'colegio_id')) {
            Schema::table('personal_educativo', function (Blueprint $table) {
                $table->unsignedBigInteger('colegio_id')->after('persona_id');
            });
        }

        if (Schema::hasColumn('personal_educativo', 'colegio_id') && !$fkExists) {
            Schema::table('personal_educativo', function (Blueprint $table) use ($fkName) {
                $table->foreign('colegio_id', $fkName)
                    ->references('id')->on('colegios')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
            });
        }

        Schema::table('personal_educativo', function (Blueprint $table) use ($hasPersonaUnique, $hasColegioIndex) {
            if (!$hasPersonaUnique) {
                $table->unique('persona_id', 'personal_educativo_persona_id_unique');
            }
            if (!$hasColegioIndex && Schema::hasColumn('personal_educativo', 'colegio_id')) {
                $table->index('colegio_id', 'personal_educativo_colegio_id_index');
            }
        });
    }

    public function down(): void
    {
        $fkName = 'personal_educativo_colegio_id_foreign';

        $indexes = collect(DB::select('SHOW INDEX FROM personal_educativo'));
        $hasPersonaUnique = $indexes->first(fn ($i) => $i->Key_name === 'personal_educativo_persona_id_unique') !== null;
        $hasColegioIndex  = $indexes->first(fn ($i) => $i->Key_name === 'personal_educativo_colegio_id_index') !== null;

        if ($hasPersonaUnique) {
            Schema::table('personal_educativo', function (Blueprint $table) {
                $table->dropUnique('personal_educativo_persona_id_unique');
            });
        }
        if ($hasColegioIndex) {
            Schema::table('personal_educativo', function (Blueprint $table) {
                $table->dropIndex('personal_educativo_colegio_id_index');
            });
        }

        $fkExists = (int) DB::selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND TABLE_NAME = 'personal_educativo'
              AND CONSTRAINT_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$fkName])->c > 0;

        if ($fkExists) {
            Schema::table('personal_educativo', function (Blueprint $table) use ($fkName) {
                $table->dropForeign($fkName);
            });
        }

        if (Schema::hasColumn('personal_educativo', 'colegio_id')) {
            Schema::table('personal_educativo', function (Blueprint $table) {
                $table->dropColumn('colegio_id');
            });
        }
    }
};
