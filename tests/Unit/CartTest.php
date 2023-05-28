<?php

namespace Tests\Unit;

use Tests\TestCase;

class CartTest extends TestCase
{
    private string $token;

    /**
     * @return void
     */
    public function test_login(): void
    {
        $payload = [
            "email" => "admin@admin.com",
            'password' => 'password'
        ];

        $response = $this->json('POST', 'api/v1/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                "token",
            ]);

        $this->token = $response["token"];
    }

    /**
     * @return void
     */
    public function test_add_to_cart()
    {
        $this->test_login();
        $payload = [
            "product_id" => 1,
            "quantity" => 1
        ];

        $this->json('POST', 'api/v1/cart', $payload, [
            'Authorization' => 'Bearer ' . $this->token
        ])->assertStatus(200)
            ->assertJsonStructure([
                "message",
            ]);
    }

    /**
     * @return void
     */
    public function test_get_cart()
    {
        $this->test_login();
        $this->get('api/v1/cart', [
            'Authorization' => 'Bearer ' . $this->token
        ])->assertStatus(200)
            ->assertJsonStructure([
                "data",
                "paginate"
            ]);
    }

    /**
     * @return void
     */
    public function test_update_cart()
    {
        $this->test_login();
        $payload = [
            "product_id" => 1,
            "quantity" => 1
        ];
        $this->json('PUT', 'api/v1/cart', $payload, [
            'Authorization' => 'Bearer ' . $this->token
        ])->assertStatus(200)
            ->assertJsonStructure([
                "message",
            ]);
    }

    /**
     * @return void
     */
    public function test_delete_cart()
    {
        $this->test_login();
        $this->delete('api/v1/cart', [], [
            'Authorization' => 'Bearer ' . $this->token
        ])->assertStatus(200)
            ->assertJsonStructure([
                "message",
            ]);
    }

}
