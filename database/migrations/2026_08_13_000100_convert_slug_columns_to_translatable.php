<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Slug alanlari artik dil bazli (TR/EN/AR ayri ayri): her dilin kendi
 * URL'i olabilsin diye tekil string kolon yerine jsonb'ye tasiniyor.
 * Mevcut degerler kaybolmadan {"tr": "eski-slug"} olarak korunur.
 * Tekillik artik veritabani kisitiyla degil, form/uygulama katmaninda
 * dil bazli kontrol edilir (bkz. HasTranslatableTabs::slugUniqueRule).
 */
return new class extends Migration
{
    private array $tables = [
        'blog_posts', 'blog_categories', 'catalogs', 'reference_projects',
        'product_categories', 'product_subcategories', 'products',
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        foreach ($this->tables as $table) {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$table}_slug_unique");
            DB::statement("ALTER TABLE {$table} ALTER COLUMN slug TYPE jsonb USING jsonb_build_object('tr', slug)");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        foreach ($this->tables as $table) {
            DB::statement("ALTER TABLE {$table} ALTER COLUMN slug TYPE varchar(255) USING (slug->>'tr')");
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_slug_unique UNIQUE (slug)");
        }
    }
};