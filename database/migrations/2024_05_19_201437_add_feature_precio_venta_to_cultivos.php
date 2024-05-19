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
            $table->decimal('precio_venta', 20, 2)->default(0)->comment('Precio de venta del cultivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cultivos', function (Blueprint $table) {
            $table->dropColumn('precio_venta');
        });
    }
};
