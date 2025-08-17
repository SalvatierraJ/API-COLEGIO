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
        Schema::table('personal_educativo', function (Blueprint $table) {
            $table->foreign(['persona_id'])->references(['id'])->on('personas')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_educativo', function (Blueprint $table) {
            $table->dropForeign('personal_educativo_persona_id_foreign');
        });
    }
};
