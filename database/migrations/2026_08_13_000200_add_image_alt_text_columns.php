<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gorsel alt metni (erisilebilirlik + SEO icin) dil bazli eklenir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->json('cover_image_alt')->nullable()->after('cover_image');
        });

        Schema::table('reference_projects', function (Blueprint $table) {
            $table->json('image_alt')->nullable()->after('image');
        });

        Schema::table('corporates', function (Blueprint $table) {
            $table->json('image_alt')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('cover_image_alt');
        });

        Schema::table('reference_projects', function (Blueprint $table) {
            $table->dropColumn('image_alt');
        });

        Schema::table('corporates', function (Blueprint $table) {
            $table->dropColumn('image_alt');
        });
    }
};
