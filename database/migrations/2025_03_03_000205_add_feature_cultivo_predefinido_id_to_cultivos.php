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
        Schema::table('cultivos', function (Blueprint $table) {
            $table->dropColumn('nombre');

			$table->unsignedBigInteger('cultivo_predefinido_id')->nullable();

			$table->foreign('cultivo_predefinido_id')->references('id')->on('cultivos_predefinidos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cultivos', function (Blueprint $table) {
			$table->dropForeign(['cultivo_predefinido_id']);
			$table->dropColumn('cultivo_predefinido_id');

			$table->string('nombre', 150);
        });
    }
};
