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
        Schema::create('orden_chat_bot', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('chat_bot_id');
            $table->unsignedBigInteger('chat_bot_predecesor_id');

            $table->foreign('chat_bot_id')->references('id')->nullOnDelete()->on('chat_bot');
            $table->foreign('chat_bot_predecesor_id')->references('id')->nullOnDelete()->on('chat_bot');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_chat_bot');
    }
};
