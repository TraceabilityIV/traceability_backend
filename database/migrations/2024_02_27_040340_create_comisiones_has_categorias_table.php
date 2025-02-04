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
            $table->unsignedBigInteger('comision_id')->nullable();
            $table->foreign('comision_id')->references('id')->on('comisiones')->nullOnDelete();

            $table->unsignedBigInteger('categorias_id')->nullable();
            $table->foreign('categorias_id')->references('id')->on('categorias')->nullOnDelete();
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
