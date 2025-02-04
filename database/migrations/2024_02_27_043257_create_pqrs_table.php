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
        Schema::create('pqrs', function (Blueprint $table) {
            $table->id();

            $table->string('nombres', 150);
            $table->string('correo', 150);
            $table->integer('telefono');

            $table->string('direccion', 150);
            $table->string('asunto', 100);
            $table->text('descripcion');

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
        Schema::dropIfExists('pqrs');
    }
};
