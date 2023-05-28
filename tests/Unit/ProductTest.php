<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * @return void
     */
    public function test_products(): void
    {
        $this->get('api/v1/product')
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    "data" => [
                        '*' => [
                            "id",
                            "name",
                            "category",
                            "description",
                            "price",
                        ],
                    ],
                ],
            );
    }
}
