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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->nullOnDelete()->on('pedidos');

            $table->unsignedBigInteger('estado_id');
            $table->foreign('estado_id')->references('id')->nullOnDelete()->on('estados');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->nullOnDelete()->on('users');

            $table->string('pasarela_id');

            $table->string('estado_pasarela', 100);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
