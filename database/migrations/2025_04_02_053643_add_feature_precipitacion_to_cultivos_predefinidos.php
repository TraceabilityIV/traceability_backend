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
            $table->decimal('precipitacion_min', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cultivos_predefinidos', function (Blueprint $table) {
            $table->dropColumn('precipitacion_min');
        });
    }
};
