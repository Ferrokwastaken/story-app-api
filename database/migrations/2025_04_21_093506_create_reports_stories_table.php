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
        Schema::create('reports_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('story_uuid')->references('uuid')->on('stories')->onDelete('cascade');
            $table->uuid('user_uuid');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports_stories');
    }
};
