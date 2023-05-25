<?php

namespace App\Http\Requests\User;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderConfirmRequest extends FormRequest
{

    /**
     * @return string[]
     */
    public function rules(): array
    {
        $status = [Order::STATUS_PENDING, Order::STATUS_APPROVED, Order::STATUS_REJECTED];
        return [
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:' . implode(',', $status),
        ];
    }
}
