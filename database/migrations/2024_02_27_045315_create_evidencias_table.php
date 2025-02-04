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
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->string('url', 255);
            $table->enum('tipo', ['imagen', 'audio', 'video', 'archivo']);

            $table->unsignedBigInteger('trazabilidad_cultivos_id')->nullable();
            $table->foreign('trazabilidad_cultivos_id')->references('id')->on('trazabilidad_cultivos')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
