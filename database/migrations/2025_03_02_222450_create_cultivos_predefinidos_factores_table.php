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
        Schema::create('cultivos_predefinidos_factores', function (Blueprint $table) {
            $table->id();

			$table->unsignedBigInteger('cultivo_predefinido_id')->nullable();
			$table->unsignedBigInteger('factor_id')->nullable();
			$table->text('descripcion')->nullable();
			$table->text('valor_apoyo')->nullable();

			$table->foreign('cultivo_predefinido_id')->references('id')->on('cultivos_predefinidos')->onDelete('set null');
			$table->foreign('factor_id')->references('id')->on('factores')->onDelete('set null');

            $table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivos_predefinidos_factores');
    }
};
