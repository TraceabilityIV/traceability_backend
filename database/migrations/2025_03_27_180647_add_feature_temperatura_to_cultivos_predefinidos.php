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
        Schema::table('cultivos_predefinidos', function (Blueprint $table) {
            $table->decimal('temperatura_min', 5, 2)->nullable();
            $table->decimal('temperatura_max', 5, 2)->nullable();
            $table->decimal('ph_min', 5, 2)->nullable();
            $table->decimal('ph_max', 5, 2)->nullable();
            $table->integer('dias_crecimiento')->nullable();
            $table->decimal('profundidad_suelo', 5, 2)->nullable();
            $table->enum('textura_suelo', [
                'arcilloso',
                'arenoso',
                'franco-arenoso',
                'franco-arcilloso',
                'franco',
                'limoso',
                'defecto arcilloso'
            ])->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cultivos_predefinidos', function (Blueprint $table) {
			$table->dropColumn('temperatura_min');
			$table->dropColumn('temperatura_max');
            $table->dropColumn('ph_min');
            $table->dropColumn('ph_max');
            $table->dropColumn('dias_crecimiento');
            $table->dropColumn('profundidad_suelo');
            $table->dropColumn('textura_suelo');
        });
    }
};
