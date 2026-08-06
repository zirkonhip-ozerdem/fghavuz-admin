<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->foreignId('product_subcategory_id')->nullable()->constrained('product_subcategories')->nullOnDelete();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->json('technical_description')->nullable();
            $table->string('series')->nullable();
            $table->string('sku')->nullable()->index();
            $table->string('cover_image')->nullable();
            // Gallery ve dokumanlar Spatie Media Library uzerinden yonetilir (collection: gallery, documents)
            $table->json('features')->nullable();
            $table->json('technical_specs')->nullable();
            $table->publishable();
            $table->boolean('is_featured')->default(false);
            $table->seoFields();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
