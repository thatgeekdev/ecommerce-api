<?php
namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{Product, User};
use Nette\Schema\Expect;

class CartOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function fluxo_completo_add_cart_confirm_order()
    {
        // Cria um utilizador e um produto
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        // Gera token do Sanctum
        $token = $user->createToken('t')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Adiciona produto ao carrinho
        $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ], $headers)->assertCreated();

        // Confirma a encomenda
        $orderResp = $this->postJson('/api/v1/checkout/confirm', [
            'shipping_address' => [
                'line1' => 'Rua A',
                'city' => 'Maputo',
                'province' => 'MP',
                'country' => 'MZ',
            ]
        ], $headers)->assertOk();

        // Valida que a resposta tem ID de pedido
        $orderId = $orderResp->json('data.id');
        $this->assertIsInt($orderId);

    }
}
