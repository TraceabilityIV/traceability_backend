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

            $table->unsignedBigInteger('cultivo_id')->nullable();
            $table->foreign('cultivo_id')->references('id')->on('cultivos')->nullOnDelete();

            $table->unsignedBigInteger('tipo_id')->comment("Subagrupadores")->nullable();
            $table->foreign('tipo_id')->references('id')->on('subagrupadores')->nullOnDelete();

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
