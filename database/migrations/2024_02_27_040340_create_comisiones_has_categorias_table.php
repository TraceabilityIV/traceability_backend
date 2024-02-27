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
        Schema::create('comisiones_has_categorias', function (Blueprint $table) {
            $table->unsignedBigInteger('comision_id');
            $table->foreign('comision_id')->references('id')->nullOnDelete()->on('comisiones');

            $table->unsignedBigInteger('categorias_id');
            $table->foreign('categorias_id')->references('id')->nullOnDelete()->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comisiones_has_categorias');
    }
};
