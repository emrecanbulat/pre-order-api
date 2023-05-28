<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ];
    }
}
