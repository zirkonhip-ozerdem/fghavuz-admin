<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporates', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('description')->nullable();
            $table->json('story_sections')->nullable();
            $table->json('mission')->nullable();
            $table->json('vision')->nullable();
            // Cok dilli, her locale kendi degerler dizisine sahip: {"tr": ["...","..."], "en": [...]}
            $table->json('values')->nullable();
            // Cok dilli timeline: {"tr": [{"year":"2010","title":"...","description":"..."}], "en": [...]}
            $table->json('milestones')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_media')->nullable();
            $table->string('image')->nullable();
            $table->publishable();
            $table->seoFields();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporates');
    }
};
