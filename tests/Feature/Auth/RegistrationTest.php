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
        // 1. Ensure the 'citizen' role exists in the database
        // We use updateOrInsert to be safe
        DB::table('roles')->updateOrInsert(
            ['name' => 'citizen', 'guard_name' => 'web'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // 2. Send the request with the STRING "citizen"
        // Since the table now exists and has this row, the validation will pass.
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '09123456789',
            'role' => 'citizen', 
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
