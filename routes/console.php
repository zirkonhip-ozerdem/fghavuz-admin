<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('deploy:prepare', function () {
    $migrateStatus = $this->call('migrate', [
        '--force' => true,
    ]);

    if ($migrateStatus !== 0) {
        return $migrateStatus;
    }

    return $this->call('db:seed', [
        '--class' => \Database\Seeders\AdminUserSeeder::class,
        '--force' => true,
    ]);
})->purpose('Run production migrations and refresh the seeded admin user');
