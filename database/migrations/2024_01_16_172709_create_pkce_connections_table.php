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
        Schema::create('pkce_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('type', 45);
            $table->string('domain', 45);
            $table->string('access_token', 800);
            $table->string('refresh_token', 800);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkce_connections');
    }
};
