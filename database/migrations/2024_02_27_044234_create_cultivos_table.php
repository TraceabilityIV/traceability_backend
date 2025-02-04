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
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 255)->index();
            $table->boolean('estado')->default(true);

            $table->string('ubicacion', 100)->nullable();
            $table->string('direccion', 150)->nullable();

            $table->string('latitud', 50)->nullable();
            $table->string('longitud', 50)->nullable();

            $table->date('fecha_siembra')->nullable();
            $table->decimal('area',11,2)->nullable();

            $table->string('variedad', 100)->nullable();
            $table->string('nombre_corto', 100)->nullable();
            $table->string('lote', 20)->nullable();
            $table->string('prefijo_registro', 20)->nullable();
            $table->date('fecha_cosecha')->nullable();

            $table->decimal('cantidad_aproximada',20,6)->nullable();

            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivos');
    }
};
