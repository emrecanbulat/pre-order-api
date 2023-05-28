<?php

namespace Tests\Unit;

use App\Models\Cart;
use Tests\TestCase;

class OrderTest extends TestCase
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
    public function test_create_order()
    {
        $this->test_login();
        $this->test_add_to_cart();

        $payload = [
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@admin.com',
            'phone' => '05469339509',
        ];

        $this->json('POST', 'api/v1/order', $payload, [
            'Authorization' => 'Bearer ' . $this->token
        ])->assertStatus(200)
            ->assertJsonStructure([
                "message",
                "details" => [
                    "order_id",
                    "status",
                ]
            ]);
    }

    /**
     * @return void
     */
    public function test_get_order()
    {
        $this->test_login();
        $this->test_add_to_cart();
        $this->test_create_order();
        $this->json('GET', 'api/v1/order', [], [
            'Authorization' => 'Bearer ' . $this->token
        ])->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    '*' => [
                        "id",
                        "order_date",
                        "status",
                        "user" => [
                            "id",
                            "name",
                            "email",
                            "phone",
                        ],
                        "products" => [
                            '*' => [
                                "name",
                                "price",
                                "quantity",
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
