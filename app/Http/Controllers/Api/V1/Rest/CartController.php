<?php

namespace App\Http\Controllers\Api\V1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CartRequest;
use App\Http\Resources\Api\CartResource;
use App\Libraries\Helper;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * @param CartRequest $request
     * @return JsonResponse
     */
    public function addToCart(CartRequest $request): JsonResponse
    {
        try {
            $cart = Cart::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->first();
            if (!empty($cart)) {
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                $data = $request->validated();
                $data["user_id"] = auth()->user()->id;
                Cart::create($data);
            }

            return new JsonResponse(['message' => 'Product added to cart'], 200);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param CartRequest $request
     * @return JsonResponse
     */
    public function removeFromCart(CartRequest $request): JsonResponse
    {
        try {
            $cart = Cart::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->first();
            if (!empty($cart)) {
                $cart->quantity -= $request->quantity;
                if ($cart->quantity <= 0) {
                    $cart->destroy($cart->id);
                }
                $cart->save();
                return new JsonResponse(['message' => 'Product removed from cart'], 200);
            }
            return new JsonResponse(['message' => 'The product you want to remove was not found in your cart'], 404);
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
        try {
            $limit = $request->get('limit', 20);
            $limit = min($limit, 100);

            $query = Cart::query();
            $query->leftJoin('products', 'products.id', '=', 'cart.product_id')
                ->select('cart.*', 'products.name as product_name', 'products.price as product_price');

            if ($request->has('product_id') && !empty($request->product_id)) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function (Builder $query) use ($search) {
                    if (is_numeric($search)) {
                        $query->orWhere('cart.id', $search);
                        $query->orWhere('products.id', $search);
                        $query->orWhere('products.price', $search);
                    }
                    $query->orWhere('products.name', 'like', '%' . $search . '%');
                    return $query;
                });
            }

            $data = Helper::getPaginationResults($query, $limit);

            return new JsonResponse([
                'data' => CartResource::collection($data),
                'paginate' => [
                    'total' => $data->total(),
                    'count' => $data->count(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'total_pages' => $data->lastPage()
                ]
            ]);
        } catch (Exception $exception) {
            logger()->error($exception->getMessage());
            return new JsonResponse(['message' => "Please check your request parameters and try again",], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function delete(): JsonResponse
    {
        try {
            Cart::where('user_id', auth()->user()->id)->delete();
            return new JsonResponse(['message' => 'Your cart has been emptied'], 200);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}
