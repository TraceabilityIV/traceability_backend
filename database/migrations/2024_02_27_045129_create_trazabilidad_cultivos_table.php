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
        Schema::create('trazabilidad_cultivos', function (Blueprint $table) {
            $table->id();

            $table->string('aplicacion', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->text('resultados')->nullable();


            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->nullOnDelete()->on('users');

            $table->unsignedBigInteger('cultivo_id');
            $table->foreign('cultivo_id')->references('id')->nullOnDelete()->on('cultivos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trazabilidad_cultivos');
    }
};
