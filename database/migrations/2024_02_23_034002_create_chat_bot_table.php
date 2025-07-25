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
        Schema::create('chat_bot', function (Blueprint $table) {
            $table->id();

            $table->string('mensaje', 100);
            $table->string('descripcion', 100);
            $table->enum('accion', ['finalizar', 'continuar', 'iniciar']);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_bot');
    }
};
