<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Product::with('currency:id,symbol')->get()]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated())->load('currency:id,symbol');

        return response()->json(['data' => $product], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->load('currency:id,symbol')]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json(['data' => $product->refresh()->load('currency:id,symbol')]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }
}
