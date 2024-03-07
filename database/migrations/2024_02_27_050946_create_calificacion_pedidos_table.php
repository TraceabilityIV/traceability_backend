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
        Schema::create('calificacion_pedidos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->nullOnDelete()->on('pedidos');

            $table->decimal('calificacion',20,2)->nullable();
            $table->text('comentario')->nullable();
            $table->text('descripcion')->nullable();

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->nullOnDelete()->on('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificacion_pedidos');
    }
};
