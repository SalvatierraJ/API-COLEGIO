<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('colegio_personals')) {
            Schema::drop('colegio_personals');
        }
    }

    public function down(): void
    {
        Schema::create('colegio_personals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('colegio_id');
            $table->unsignedBigInteger('personal_educativo_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('colegio_id')->references('id')->on('colegios')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('personal_educativo_id')->references('id')->on('personal_educativo')->cascadeOnUpdate()->restrictOnDelete();
            $table->unique(['colegio_id', 'personal_educativo_id']);
        });
    }
};
