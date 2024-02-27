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
        Schema::create('galerias', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->nullable();
            $table->string('url', 255)->nullable();
            $table->enum("tipo", ["imagen", "audio", "video", "archivo"]);

            $table->unsignedBigInteger('cultivo_id');
            $table->foreign('cultivo_id')->references('id')->nullOnDelete()->on('cultivos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galerias');
    }
};
