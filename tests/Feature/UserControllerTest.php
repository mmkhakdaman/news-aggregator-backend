<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->json('POST', '/api/auth/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_login_user()
    {
        $user =  User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $userData = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this->json('POST', '/api/auth/login', $userData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['user', 'token']]);
    }
}
