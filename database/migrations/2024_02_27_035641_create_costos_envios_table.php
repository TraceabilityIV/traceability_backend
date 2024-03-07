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
        Schema::create('costos_envios', function (Blueprint $table) {
            $table->id();

            $table->decimal('costo', 20, 2);
            $table->boolean('estado')->default(true);

            $table->unsignedBigInteger('tipo_costo_id')->comment("Subagrupador");
            $table->foreign('tipo_costo_id')->references('id')->nullOnDelete()->on('subagrupadores');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costos_envios');
    }
};
