<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ConfirmOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->latest()->paginate($request->get('per_page', 10));
        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        $order->load('items');
        return new OrderResource($order);
    }

    public function confirm(ConfirmOrderRequest $request)
    {
        $order = $this->orderService->confirmFromCart(
            $request->user()->id,
            $request->validated()['shipping_address'],
            $request->validated()['billing_address'] ?? null,
        );
        return (new OrderResource($order))->additional(['message' => 'Encomenda criada e a aguardar pagamento']);
    }
}