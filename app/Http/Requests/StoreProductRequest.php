<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'decimal:0,2', 'min:0', 'max:999999999999.99'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['required', 'decimal:0,2', 'min:0', 'max:999999999999.99'],
            'manufacturing_cost' => ['required', 'decimal:0,2', 'min:0', 'max:999999999999.99'],
            'manufacuring_date_create' => ['nullable', 'date_format:Y-m-d'],
            'manufacuring_date_expire' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:manufacuring_date_create'],
        ];
    }
}
