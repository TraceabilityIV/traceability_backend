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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();

            $table->string('direccion', 255)->index();
            $table->string('latitud', 50)->nullable();
            $table->string('longitud', 50)->nullable();

            $table->string('receptor', 100)->nullable();
            $table->boolean('estado')->default(true);

            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('barrio_id')->nullable();
            $table->foreign('barrio_id')->references('id')->on('barrios')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
