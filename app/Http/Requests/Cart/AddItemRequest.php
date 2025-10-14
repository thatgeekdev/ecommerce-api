<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return ['product_id' => ['required', 'exists:products,id'], 'quantity' => ['required', 'integer', 'min:1', 'max:100'],];
    }
}
