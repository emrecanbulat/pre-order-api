<?php

namespace App\Http\Controllers\Api\V1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\OrderConfirmRequest;
use App\Http\Requests\User\OrderRequest;
use App\Http\Resources\Api\OrderResourceAdmin;
use App\Http\Resources\Api\OrderResourceUser;
use App\Libraries\Helper;
use App\Libraries\MessageHelper;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin', ['only' => ['confirmOrder']]);
    }

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
                OrderProduct::create([
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 20);
        $limit = min($limit, 100);

        if (auth()->user()->isAdmin()) {
            $query = Order::query();
        } else {
            $query = Order::where('user_id', auth()->user()->id);
        }

        $query->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.name as user_full_name');

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function (Builder $query) use ($search) {
                if (is_numeric($search)) {
                    $query->orWhere('orders.id', $search);
                    $query->orWhere('users.id', $search);
                }
                $query->orWhere('users.name', 'like', '%' . $search . '%');
                $query->orWhere('orders.status', 'like', '%' . $search . '%');
                return $query;
            });
        }

        $data = Helper::getPaginationResults($query, $limit);

        if (auth()->user()->isAdmin()) {
            $data = OrderResourceAdmin::collection($data);
        } else {
            $data = OrderResourceUser::collection($data);
        }

        return new JsonResponse([
            'data' => $data,
            'paginate' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage()
            ]
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        if (auth()->user()->isAdmin()) {
            $order = Order::find($id);
            if (!empty($order)) {
                $order->user_full_name = $order->user->name;
                return new JsonResponse([
                    'data' => new OrderResourceAdmin($order),
                ]);
            }
        } else {
            $order = Order::where('user_id', auth()->user()->id)->where('id', $id)->first();
            if (!empty($order)) {
                return new JsonResponse([
                    'data' => new OrderResourceUser($order),
                ]);
            }
        }
        return new JsonResponse(['message' => 'Order not found'], 404);
    }

    /**
     * @param OrderConfirmRequest $request
     * @return JsonResponse
     */
    public function confirmOrder(OrderConfirmRequest $request): JsonResponse
    {
        try {
            $order = Order::find($request->order_id);
            if ($order) {
                $order->status = $request->status;
                $order->save();
            }
            MessageHelper::sendMessage(MessageHelper::APPROVED_MESSAGE, $order->user->phone_number);

            return new JsonResponse(['message' => 'Order status updated successfully']);
        } catch (ConfigurationException $e) {
            logger()->info('Twilio configuration error: ' . $e->getMessage());
            return new JsonResponse(['message' => 'Order status updated successfully']);
        } catch (TwilioException $e) {
            logger()->info('Twilio error: ' . $e->getMessage());
            return new JsonResponse(['message' => 'Order status updated successfully']);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}
