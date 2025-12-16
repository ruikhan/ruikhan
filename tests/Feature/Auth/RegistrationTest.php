<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

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
        // 1. Create the 'resident' role in the database
        // We use 'resident' because your Controller specifically requires it
        DB::table('roles')->insertOrIgnore([
            'name' => 'resident', 
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Send the registration request
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '09123456789',
            'role' => 'resident', // FIX: Added back, and matches the 'in:resident...' rule
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
