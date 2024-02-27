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
        Schema::create('barrios', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->index();
            $table->string('nombre_corto', 50)->index();
            $table->boolean('estado')->default(true);
            $table->mediumInteger('codigo_postal')->nullable();

            $table->unsignedBigInteger('ciudad_id');

            $table->foreign('ciudad_id')->references('id')->nullOnDelete()->on('ciudades');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barrios');
    }
};
