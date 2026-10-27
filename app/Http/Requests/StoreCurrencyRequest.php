<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', 'unique:currencies,name'],
            'symbol' => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'decimal:0,6', 'min:0'],
            'exchange_rate_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
