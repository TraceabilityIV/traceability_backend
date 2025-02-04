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
        Schema::create('factores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->index();
            $table->string('descripcion', 255);
            $table->decimal('peso')->default(0);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->unsignedBigInteger('barrio_id')->nullable();
            $table->unsignedBigInteger('ciudad_id')->nullable();
            $table->unsignedBigInteger('departamento_id')->nullable();
            $table->unsignedBigInteger('pais_id')->nullable();
            $table->string('latitud', 30);
            $table->string('longitud', 30);
            $table->decimal('radio')->default(0);
            $table->text('poligono');

            $table->foreign('barrio_id')->references('id')->on('barrios')->onDelete('set null');
            $table->foreign('ciudad_id')->references('id')->on('ciudades')->onDelete('set null');
            $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('set null');
            $table->foreign('pais_id')->references('id')->on('paises')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factores');
    }
};
