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
        Schema::create('historial_estados_pedidos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->nullOnDelete()->on('pedidos');

            $table->unsignedBigInteger('estado_id');
            $table->foreign('estado_id')->references('id')->nullOnDelete()->on('estados');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->nullOnDelete()->on('users');

            $table->text('justificacion')->nullable();

            $table->unsignedBigInteger('estado_siguiente_id');
            $table->foreign('estado_siguiente_id')->references('id')->nullOnDelete()->on('estados');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estados_pedidos');
    }
};
