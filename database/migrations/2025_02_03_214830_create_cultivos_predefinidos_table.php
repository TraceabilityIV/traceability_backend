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
        Schema::create('cultivos_predefinidos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('nombre_corto', 30);
            $table->unsignedBigInteger('categoria_id')->nullable();

            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');

            $table->unsignedBigInteger('subcategoria_id')->nullable();

            $table->foreign('subcategoria_id')->references('id')->on('categorias')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivos_predefinidos');
    }
};
