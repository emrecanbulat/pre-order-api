<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResourceAdmin extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_date' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'status' => $this->status,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user_full_name,
                'email' => $this->user_email,
                'phone' => $this->user_phone,
            ],
            'products' => $this->products()->select('name', 'price', 'quantity')->get(),
        ];
    }
}
