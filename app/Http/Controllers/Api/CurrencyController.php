<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Currency::query()->orderBy('name')->get()]);
    }

    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = Currency::create($request->validated());

        return response()->json(['data' => $currency], 201);
    }

    public function show(Currency $currency): JsonResponse
    {
        return response()->json(['data' => $currency]);
    }
}
