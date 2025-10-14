<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{Product, User, Order, CartItem, Payment};

class FullApiFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function full_flow_from_product_creation_to_order_delivery()
    {
        // 1. Criar utilizador
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 2. Criar produtos
        $products = Product::factory(3)->create(['stock' => 10]);

        // 3. Adicionar produtos ao carrinho
        foreach ($products as $product) {
            $this->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ], $headers)->assertCreated();
        }

        // 4. Verificar carrinho
        $cartResp = $this->getJson('/api/v1/cart', $headers)->assertOk();
        $this->assertCount(3, $cartResp->json('data.items'));
        

        // 5. Confirmar a encomenda
        $orderResp = $this->postJson('/api/v1/checkout/confirm', [
            'shipping_address' => [
                'line1' => 'Rua A',
                'city' => 'Maputo',
                'province' => 'MP',
                'country' => 'MZ',
            ]
        ], $headers)->assertOk();

        $orderId = $orderResp->json('data.id');
        $this->assertIsInt($orderId);

        /** @var Order $order */
        $order = Order::findOrFail($orderId);

        // 6. Iniciar pagamento via MPesa
        $paymentResp = $this->postJson("/api/v1/orders/{$order->id}/payments/init", [
            'provider' => 'mpesa'
        ], $headers)->assertCreated();

        $paymentId = $paymentResp->json('payment.id');
        $this->assertDatabaseHas('payments', [
            'id' => $paymentId,
            'order_id' => $order->id,
            'status' => 'pending'
        ]);

        /** @var Payment $payment */
        $payment = Payment::findOrFail($paymentId);

        // 7. Simular webhook de pagamento capturado
        $this->postJson("/api/v1/payments/webhook/mpesa", [
            'payment_id' => $payment->id,
            'status' => 'captured'
        ])->assertOk();

        $payment->refresh();
        $order->refresh();

        $this->assertEquals('captured', $payment->status);
        $this->assertEquals('paid', $order->status);

        // 8. Atualizar status de envio e entrega
        $order->update(['status' => 'shipped']);
        $this->assertEquals('shipped', $order->status);

        $order->update(['status' => 'delivered']);
        $this->assertEquals('delivered', $order->status);
    }
}
