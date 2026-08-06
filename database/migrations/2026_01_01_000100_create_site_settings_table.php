<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('FGPOOL');
            $table->string('logo')->nullable();
            $table->string('dark_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('footer_logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('default_locale', 5)->default('tr');
            // Ornek: ["tr","en","ar"]
            $table->json('active_locales')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            // Cok dilli: {"tr": "...", "en": "...", "ar": "..."}
            $table->json('footer_text')->nullable();
            $table->string('copyright_text')->nullable();
            $table->string('yengec_yazilim_url')->nullable();
            $table->string('yna_ekibi_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
