<?php

namespace App\Http\Controllers\Api\V1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Libraries\Helper;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 20);
        $limit = min($limit, 100);

        $query = Product::query();
        $query->leftJoin('categories', 'categories.id', '=', 'products.category_id')
        ->select('products.*', 'categories.name as category_name');

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function (Builder $query) use ($search) {
                if (is_numeric($search)) {
                    $query->orWhere('id', $search);
                    $query->orWhere('products.price', $search);
                }
                $query->orWhere('products.name', 'like', '%' . $search . '%');
                $query->orWhere('products.description', 'like', '%' . $search . '%');
                return $query;
            });
        }

        $data = Helper::getPaginationResults($query, $limit);

        return new JsonResponse([
            'data' => ProductResource::collection($data),
            'paginate' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage()
            ]
        ]);
    }
}
