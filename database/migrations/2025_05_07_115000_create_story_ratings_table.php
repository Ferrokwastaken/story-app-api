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
        Schema::create('story_ratings', function (Blueprint $table) {
            $table->id();
            $table->uuid('story_uuid');
            $table->unsignedBigInteger('rating')->check('rating >= 1 AND rating <= 5');
            $table->timestamps();

            $table->foreign('story_uuid')->references('uuid')->on('stories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_ratings');
    }
};
