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
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->index();
            $table->string('nombre_corto', 50)->index();
            $table->boolean('estado')->default(true);
            $table->string('bandera', 255)->nullable();
            $table->string('indicador', 5)->nullable();
            $table->mediumInteger('codigo_postal')->nullable();

            $table->unsignedBigInteger('departamento_id')->nullable();

            $table->foreign('departamento_id')->references('id')->on('departamentos')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};
