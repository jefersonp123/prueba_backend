<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                Rule::notIn([$this->route('product')->currency_id]),
                Rule::unique('product_prices')->where('product_id', $this->route('product')->id),
            ],
            'price' => ['required', 'decimal:0,2', 'min:0', 'max:999999999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency_id.not_in' => 'La divisa del precio debe ser distinta de la divisa base del producto.',
            'currency_id.unique' => 'El producto ya tiene un precio registrado en esa divisa.',
        ];
    }
}
