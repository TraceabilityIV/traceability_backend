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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->index();
            $table->string('id_referencia', 255)->index();

            $table->string('icono', 200)->nullable();
            $table->string('color', 20)->nullable();
            $table->enum('tipo_icono', ['imagen', 'clase'])->nullable();

            $table->boolean('estado')->default(true);

            $table->unsignedBigInteger('permiso_id');
            $table->foreign('permiso_id')->references('id')->nullOnDelete()->on('permisos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
