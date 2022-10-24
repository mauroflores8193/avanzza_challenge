<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ApiRequestValidateTest extends TestCase {
    public function test_no_more_queries() {
        $user = User::all()->first();
        $token = $this->getToken($user);
        $maxQueries = 3;
        for($i = 0; $i < $maxQueries; $i++) {
            $validQueryResponse = $this->withHeaders(['Authorization' => "Bearer $token"])->get('/api/me');
            $validQueryResponse->assertStatus(200);
        }
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get('/api/me');
        $response->assertUnauthorized();
        $this->travel(1)->minutes();
        $this->travel(1)->seconds();
        $validQueryResponse = $this->withHeaders(['Authorization' => "Bearer $token"])->get('/api/me');
        $validQueryResponse->assertStatus(200);
    }
}
