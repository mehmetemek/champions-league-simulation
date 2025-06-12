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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->unique()->constrained('fixtures')->onDelete('cascade');
            $table->unsignedInteger('home_score');
            $table->unsignedInteger('away_score');
            $table->timestamp('played_at');
            $table->unsignedInteger('home_shoot_count')->nullable();
            $table->unsignedInteger('away_shoot_count')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};