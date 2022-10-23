<?php

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase {

    public function test_login_ok() {
        $user = User::all()->first();
        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password']);
        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', 1)
                    ->etc();
            });
    }

    public function test_login_password_failed() {
        $user = User::all()->first();
        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'fait']);
        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', 0)
                    ->where('msg', 'Credenciales incorrectas.');
            });
    }

    public function test_login_user_failed() {
        $email_failed = 'fail@gmail.com';
        $response = $this->postJson('/api/login', ['email' => $email_failed, 'password' => 'password']);
        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', 0)
                    ->where('msg', 'Usuario no encontrado.');
            });
    }

    public function test_me() {
        $user = User::all()->first();
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->getToken($user)}"])->get('/api/me');
        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($user) {
                $json->where('name', $user->name)
                    ->where('email', $user->email)
                    ->etc();
            });
    }
}
