<?php

namespace App\Http\Controllers\Api\V1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\OrderRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function makeOrder(OrderRequest $request): JsonResponse
    {
        try {
            $cart = Cart::where('user_id', auth()->user()->id)->get();
            if ($cart->count() == 0) {
                return new JsonResponse(['message' => 'Cart is empty'], 400);
            }

            $order = Order::create([
                'user_id' => auth()->user()->id,
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }

            Cart::where('user_id', auth()->user()->id)->delete();
            return new JsonResponse(['message' => 'Order created successfully', 'details' => ['order_id' => $order->id, 'status' => $order->status]]);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}
