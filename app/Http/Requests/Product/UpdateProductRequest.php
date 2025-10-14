<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-products');
    }
    public function rules(): array
    {
        $productId = $this->route('product')->id;
        return [
            'category_id' => ['nullable', 'exists:categories,id'], 
            'sku' => ['nullable', 'string', 'max:64', "unique:products,sku,{$productId}"], 
            'name' => ['sometimes', 'string', 'max:255'], 
            'slug' => ['sometimes', 'string', 'max:255', "unique:products,slug,{$productId}"], 
            'description' => ['nullable', 'string'], 
            'price' => ['sometimes', 'numeric', 'min:0'], 
            'stock' => ['sometimes', 'integer', 'min:0'], 
            'is_active' => ['boolean'], 
            'images' => ['array'], 
            'images.*.url' => ['required', 'url'], 
            'images.*.alt' => ['nullable', 'string', 'max:255'],
        ];
    }
}
