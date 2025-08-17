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
        Schema::create('instituciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comuna_id')->index();
            $table->string('nombre', 191);
            $table->string('rut', 32)->unique();
            $table->string('telefono', 50)->nullable();
            $table->string('estado', 50)->nullable();
            $table->boolean('delete_status')->default(false);
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instituciones');
    }
};
