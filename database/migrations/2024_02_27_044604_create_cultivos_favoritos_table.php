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
        Schema::create('cultivos_favoritos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cultivo_id');
            $table->foreign('cultivo_id')->references('id')->nullOnDelete()->on('cultivos');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->nullOnDelete()->on('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivos_favoritos');
    }
};
