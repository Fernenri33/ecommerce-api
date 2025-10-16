<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_login(): void
    {
        $rol = Role::factory()->create([
            'name' => 'admin',
            'description' => 'admin'
        ]);

        $user = User::factory()->create([
            'email' => 'test@ejemplo.com',
            'password' => Hash::make('password123'),
            'name' => 'Admin',
            'last_name' => 'test',
            'rol_id' => $rol->id
        ]);

        $response = $this->postJson('api/login', [
            'email' => 'test@ejemplo.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'content' => ['token', 'token_type', 'expires_in']
                 ]);

        $json = $response->json();

        // verifica éxito y que exista token
        $this->assertTrue(data_get($json, 'success') === true);
        $this->assertNotEmpty(data_get($json, 'content.token'));
        dump($json); // opcional para depuración
    }
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $rol = Role::factory()->create();
        $user = User::factory()->create([
            'email' => 'test@ejemplo.com',
            'password' => Hash::make('password123'),
            'name' => 'Admin',
            'last_name' => 'test',
            'rol_id' => $rol->id
        ]);
        $response = $this->postJson('api/login', [
            'email' => 'someone@ejemplo.com',
            'password' => 'wrong-password',
        ]);
        $json = $response->json();
        $response->assertStatus(401)->assertJsonStructure(['message']);
        dump($json);
    }
}
