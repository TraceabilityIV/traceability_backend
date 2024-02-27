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
        Schema::create('trazabilidad_transportes', function (Blueprint $table) {
            $table->id();

            $table->text('descripcion')->nullable();
            $table->timestamp('fecha');

            $table->text('observaciones')->nullable();
            $table->boolean("flag_entregado")->default(false);

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->nullOnDelete()->on('users');

            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->nullOnDelete()->on('pedidos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trazabilidad_transportes');
    }
};
