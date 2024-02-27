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
        Schema::create('categorias_has_costos_envios', function (Blueprint $table) {

            $table->unsignedBigInteger('costos_envio_id');
            $table->foreign('costos_envio_id')->references('id')->nullOnDelete()->on('costos_envios');

            $table->unsignedBigInteger('categorias_id');
            $table->foreign('categorias_id')->references('id')->nullOnDelete()->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_has_costos_envios');
    }
};
