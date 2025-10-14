<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id, 
            'category_id' => $this->category_id, 
            'sku' => $this->sku, 
            'name' => $this->name, 
            'slug' => $this->slug, 
            'description' => $this->description, 
            'price' => (float) $this->price, 
            'stock' => (int) $this->stock, 
            'is_active' => (bool) $this->is_active, 
            'images' => $this->whenLoaded('images', fn() => $this->images->map(fn($img) => ['url' => $img->url, 'alt' => $img->alt, 'position' => $img->position,])), 
            'created_at' => $this->created_at,
        ];
    }
}
