<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            SiteSettingSeeder::class,
            SeoPageSeeder::class,
            CorporateSeeder::class,
            ProductCategorySeeder::class,
            BlogCategorySeeder::class,
            CatalogSeeder::class,
            ReferenceProjectSeeder::class,
        ]);
    }
}
