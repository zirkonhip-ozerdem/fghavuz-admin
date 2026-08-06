<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_SEED_EMAIL', 'admin@fgpool.com');
        $password = env('ADMIN_SEED_PASSWORD', 'password');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'FGPOOL Admin',
                'password' => $password,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole(Permissions::ROLE_SUPER_ADMIN);

        $this->command?->warn("Admin kullanıcı: {$email} / {$password} — ilk girişten sonra şifreyi değiştirin!");
    }
}
