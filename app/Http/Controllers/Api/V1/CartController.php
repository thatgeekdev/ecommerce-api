<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemRequest;
use App\Models\{Cart, CartItem};
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}
    
    public function show(Request $request)
    {
        $cart = $this->cartService->getOrCreateForUser($request->user()->id)->load('items.product');
        return response()->json($cart);
    }

    public function addItem(AddItemRequest $request)
    {
        $cart = $this->cartService->getOrCreateForUser($request->user()->id);
        $cart = $this->cartService->addItem($cart, (int)$request->product_id, (int)$request->quantity)->load('items.product');
        return response()->json($cart, 201);
    }

    public function updateItem(Request $request, int $itemId)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:100']]);
        $cart = $this->cartService->getOrCreateForUser($request->user()->id);
        $cart = $this->cartService->updateItem($cart, $itemId, (int)$data['quantity'])->load('items.product');
        return response()->json($cart);
    }

    public function removeItem(Request $request, int $itemId)
    {
        $cart = $this->cartService->getOrCreateForUser($request->user()->id);
        $item = $cart->items()->whereKey($itemId)->firstOrFail();
        $item->delete();
        return response()->json(['message' => 'Item removido']);
    }

    public function clear(Request $request)
    {
        $cart = $this->cartService->getOrCreateForUser($request->user()->id);
        $cart->items()->delete();
        return response()->json(['message' => 'Carrinho limpo']);
    }
}
