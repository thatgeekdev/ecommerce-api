<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-products');
    }
    public function rules(): array
    {
        return ['category_id' => ['nullable', 'exists:categories,id'], 'sku' => ['nullable', 'string', 'max:64', 'unique:products,sku'], 'name' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:255', 'unique:products,slug'], 'description' => ['nullable', 'string'], 'price' => ['required', 'numeric', 'min:0'], 'stock' => ['required', 'integer', 'min:0'], 'is_active' => ['boolean'], 'images' => ['array'], 'images.*.url' => ['required', 'url'], 'images.*.alt' => ['nullable', 'string', 'max:255'],];
    }
}
