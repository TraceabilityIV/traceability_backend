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
        Schema::create('mensajes_chat', function (Blueprint $table) {
            $table->id();
            $table->text('mensaje');
            $table->enum("tipo", ['texto', 'audio', 'imagen', 'video', 'archivo']);

            $table->unsignedBigInteger('usuario_envia_id')->nullable();
            $table->foreign('usuario_envia_id')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('usuario_recibe_id')->nullable();
            $table->foreign('usuario_recibe_id')->references('id')->on('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes_chat');
    }
};
