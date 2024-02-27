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
        Schema::create('adjuntos_pqrs', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 255);
            $table->string('url', 255);
            $table->enum('tipo', ['imagen', 'audio', 'video', 'archivo']);

            $table->unsignedBigInteger('pqrs_id');
            $table->foreign('pqrs_id')->references('id')->nullOnDelete()->on('pqrs');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuntos_pqrs');
    }
};
