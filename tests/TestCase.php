<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase {
    use RefreshDatabase;
    use CreatesApplication;

    protected $seed = true;

    protected function getToken($user = false) {
        if(!$user) {
            $user = User::all()->first();
        }
        $login_response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password']);
        return $login_response->json()['msg'];
    }
}
