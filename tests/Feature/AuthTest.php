<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase {

    use RefreshDatabase;

    public function test_login_ok() {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password']);
        $response->assertStatus(200);
        $response->assertJson(function (AssertableJson $json) {
            $json->where('status', 1)
                ->etc();
        });;
    }

    public function test_password_failed() {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'fait']);
        $response->assertStatus(200);
        $response->assertJson(function (AssertableJson $json) {
            $json->where('status', 0)
                ->where('msg', 'Credenciales incorrectas.');
        });;
    }

    public function test_user_failed() {
        $email_failed = 'fail@gmail.com';
        $response = $this->postJson('/api/login', ['email' => $email_failed, 'password' => 'password']);
        $response->assertStatus(200);
        $response->assertJson(function (AssertableJson $json) {
            $json->where('status', 0)
                ->where('msg', 'Usuario no encontrado.');
        });;
    }
}
