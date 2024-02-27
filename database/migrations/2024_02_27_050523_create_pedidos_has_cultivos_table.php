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
        Schema::create('pedidos_has_cultivos', function (Blueprint $table) {

            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->nullOnDelete()->on('pedidos');

            $table->unsignedBigInteger('cultivo_id');
            $table->foreign('cultivo_id')->references('id')->nullOnDelete()->on('cultivos');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_has_cultivos');
    }
};
