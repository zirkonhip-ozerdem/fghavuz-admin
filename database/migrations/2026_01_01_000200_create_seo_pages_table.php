<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            // home, corporate, products, catalog, blog, contact, quote, ...
            $table->string('page_key');
            $table->string('locale', 5);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots')->nullable()->default('index, follow');
            $table->json('schema_json')->nullable();
            $table->timestamps();

            $table->unique(['page_key', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};
