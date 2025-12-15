<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        // 1. Check if the roles table exists. If not, we skip the role check to avoid crashing.
        if (Schema::hasTable('roles')) {
            DB::table('roles')->insertOrIgnore([
                'name' => 'citizen',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Send the registration request
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '09123456789',
            'role' => 'citizen', // If roles table is missing, validation might fail here or pass depending on your logic
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // 3. If the role validation fails due to missing table, we can't easily fix it in a unit test without the migration file.
        // However, usually RefreshDatabase handles this.
        // If this still fails, it means you have a migration file missing from git.
        
        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
