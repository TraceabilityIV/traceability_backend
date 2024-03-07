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
        Schema::create('precios', function (Blueprint $table) {
            $table->id();

            $table->boolean('estado')->default(true);
            $table->decimal('precio_venta', 20, 2);

            $table->unsignedBigInteger('cultivo_id');
            $table->foreign('cultivo_id')->references('id')->nullOnDelete()->on('cultivos');

            $table->unsignedBigInteger('tipo_id')->comment("Subagrupadores");
            $table->foreign('tipo_id')->references('id')->nullOnDelete()->on('subagrupadores');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
