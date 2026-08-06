<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_inactive_or_roleless_user_cannot_access_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_super_admin_can_access_panel(): void
    {
        Role::firstOrCreate(['name' => Permissions::ROLE_SUPER_ADMIN, 'guard_name' => 'web']);

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Permissions::ROLE_SUPER_ADMIN);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
    }

    public function test_deactivated_admin_cannot_access_panel(): void
    {
        Role::firstOrCreate(['name' => Permissions::ROLE_SUPER_ADMIN, 'guard_name' => 'web']);

        $user = User::factory()->create(['is_active' => false]);
        $user->assignRole(Permissions::ROLE_SUPER_ADMIN);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }
}
