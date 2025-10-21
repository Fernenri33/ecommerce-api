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
    public function test_user_can_logout(): void
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

        $login = $this->postJson('api/login', [
            'email' => 'test@ejemplo.com',
            'password' => 'password123',
        ]);

        $login->assertStatus(200);

        $jsonLogin = $login->json();
        $token = data_get($jsonLogin, 'content.token');
        $this->assertNotEmpty($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/logout');

        $response->assertStatus(200)->assertJson(['message' => 'Logout exitoso']);

        $json = $response->json();

        // Verificar que los tokens del usuario fueron revocados
        $this->assertEquals(0, $user->tokens()->count());

        dump($json);
    }
    public function test_user_can_register(){

        $rol = Role::factory()->create([
            'name' => 'customer',
            'description' => 'customer',
            'id' => 2
        ]);

        $response = $this->postJson('api/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertStatus(201)->assertJson(['message' => 'Usuario registrado exitosamente']);
        $json = $response->json();
        dump($json);
        
    }
    public function test_user_cannot_register_without_password_confirmation(){

        $rol = Role::factory()->create([
            'name' => 'customer',
            'description' => 'customer',
            'id' => 2
        ]);

        $response = $this->postJson('api/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'otrapassword'
        ]);
        $response->assertStatus(422)->assertJson(['message' => 'Errores de validación']);
        $json = $response->json();
        dump($json);
        
    }
        public function test_user_cannot_register_with_same_email(){

        $rol = Role::factory()->create([
            'name' => 'customer',
            'description' => 'customer',
            'id' => 2
        ]);

        $user1 = $this->postJson('api/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response = $this->postJson('api/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertStatus(422)->assertJson(['message' => 'Errores de validación']);
        $json = $response->json();
        dump($json);
        
    }
}
