<?php

namespace Tests\Unit;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * @return void
     */
    public function test_login(): void
    {
        $payload = [
            "email" => "admin@admin.com",
            'password' => 'password'
        ];

        $this->json('POST', 'api/v1/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                "token",
            ]);
    }
}
