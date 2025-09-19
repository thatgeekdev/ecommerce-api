<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'shipping_total' => (float) $this->shipping_total,
            'tax_total' => (float) $this->tax_total,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'items' => $this->whenLoaded('items', fn() => $this->items->map(fn($it) => ['product_id' => $it->product_id, 'name' => $it->name, 'unit_price' => (float) $it->unit_price, 'quantity' => (int) $it->quantity, 'total' => (float) $it->total,])),
            'created_at' => $this->created_at,
        ];
    }
}
