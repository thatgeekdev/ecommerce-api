<?php
namespace App\Services;

use App\Models\Cart, CartItems, Products;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    public function getOrCreateForUser(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId], ['currency' => 'MZN']);
    }

    public function addItem(Cart $cart, int $productId, int $quantity): Cart
    {
        return DB::transaction(function () use ($cart, $productId, $quantity) {
            $product = Product::query()->where('is_active', true)->findOrFail($productId);
            if ($product->stock < $quantity) {
                abort(422, 'Stock insuficiente.');
            }
            $unit = $product->price;
            $item = $cart->items()->firstOrNew(['product_id' => $productId]);
            $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
            $item->unit_price = $unit;
            $item->total = $item->quantity * $unit;
            $item->save();
            return $cart->fresh('items');
        });
    }

    public function updateItem(Cart $cart, int $itemId, int $quantity): Cart
    {
        return DB::transaction(function () use ($cart, $itemId, $quantity) {
            $item = $cart->items()->whereKey($itemId)->firstOrFail();
            if ($quantity < 1) {
                $item->delete();
                return $cart->fresh('items');
            }
            if ($item->product->stock < $quantity) {
                abort(422, 'Stock insuficiente.');
            }
            $item->quantity = $quantity;
            $item->total = $quantity * $item->unit_price;
            $item->save();
            return $cart->fresh('items');
        });
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}