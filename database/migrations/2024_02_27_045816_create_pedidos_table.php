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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('direccion_id')->nullable();
            $table->foreign('direccion_id')->references('id')->on('direcciones')->nullOnDelete();

            $table->unsignedBigInteger('estado_pedido_id')->nullable();
            $table->foreign('estado_pedido_id')->references('id')->on('estados')->nullOnDelete();

            $table->decimal('total', 20, 2);
            $table->decimal('subtotal', 20, 2);
            $table->decimal('saldo', 20, 2);

            $table->string("metodo_pago", 20);
            $table->enum("tipo_pago", ['parcial', 'total']);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
