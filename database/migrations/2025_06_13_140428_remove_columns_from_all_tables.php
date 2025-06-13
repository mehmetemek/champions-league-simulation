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
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'played_at']);
        });

        Schema::table('score_boards', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('fixtures', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamp('played_at')->after('away_score');
        });

        Schema::table('score_boards', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
