<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase {
    use RefreshDatabase;

    public function test_user_can_register(): void {
        $response = $this->postJson('/api/register', [
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_user_can_login(): void {
        $user = User::factory()->create([
            'password' => bcrypt('123456'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type']);
    }

    public function test_user_cannot_login_with_wrong_password(): void {
        $user = User::factory()->create([
            'password' => bcrypt('123456'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401);
    }
}