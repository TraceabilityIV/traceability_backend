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
        Schema::table('historial_estados_pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_siguiente_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_estados_pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_siguiente_id')->nullable(false)->change();
        });
    }
};
