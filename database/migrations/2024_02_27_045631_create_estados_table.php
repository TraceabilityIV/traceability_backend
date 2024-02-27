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
        Schema::create('estados', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->index();
            $table->string('icono_cumplido', 255)->nullable();
            $table->string('icono', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->boolean('flag_inicial')->default(false);
            $table->boolean('flag_final')->default(false);

            $table->unsignedBigInteger('estado_siguiente_id');
            $table->foreign('estado_siguiente_id')->references('id')->nullOnDelete()->on('estados');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
