<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductPriceController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->prices()->with('currency')->get()]);
    }

    public function store(StoreProductPriceRequest $request, Product $product): JsonResponse
    {
        $price = $product->prices()->create($request->validated())->load('currency');

        return response()->json(['data' => $price], 201);
    }
}
