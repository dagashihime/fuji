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
        Schema::create('anime_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('anime_id');
            $table->string('status', 45);
            $table->integer('score');
            $table->integer('num_episodes_watched');
            $table->boolean('is_rewatching');
            $table->integer('num_times_rewatched');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_list_items');
    }
};
