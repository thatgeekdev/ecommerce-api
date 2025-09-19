<?php

namespace App\Services;

use App\Models\{Cart, Order, OrderItem, Product};
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function confirmFromCart(int $userId, array $shippingAddress, ?array $billingAddress = null): Order
    {
        return DB::transaction(function () use ($userId, $shippingAddress, $billingAddress) {
            $cart = Cart::with('items.product')->where('user_id', $userId)->firstOrFail();
            if ($cart->items->isEmpty()) {
                abort(422, 'Carrinho vazio.');
            }
            $subtotal = $cart->items->sum('total');
            $shipping = 0;
            // TODO: cálculo real de envio 
            $tax = 0;
            // TODO: impostos
            $total = $subtotal + $shipping + $tax;
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'awaiting_payment',
                'subtotal' => $subtotal,
                'shipping_total' => $shipping,
                'tax_total' => $tax,
                'total' => $total,
                'currency' => 'MZN',
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress ?? $shippingAddress,
            ]);
            foreach ($cart->items as $ci) {
                if ($ci->product->stock < $ci->quantity) {
                    abort(422, 'Stock insuficiente para um item.');
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $ci->product_id,
                    //  'name' => $ci->product->name,
                    'unit_price' => $ci->unit_price,
                    'quantity' => $ci->quantity,
                    'total' => $ci->total,
                ]);
                // reserva de stock (simples)
                $ci->product->decrement('stock', $ci->quantity);
            }
            // limpa carrinho
            $cart->items()->delete();
            return $order->fresh(['items']);
        });
    }
}
