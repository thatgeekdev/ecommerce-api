<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\{User, Product, Order, CartItem, Payment};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class E2EFlowTest extends TestCase
{
    use RefreshDatabase;
/** @test */
    public function test_user_can_register_and_login()
    {
        $payload = ['name'=>'Jose','email'=>'jose@example.com','password'=>'secret123'];

        $this->postJson('/api/v1/auth/register', $payload)->assertCreated();

        $login = $this->postJson('/api/v1/auth/login', ['email'=>$payload['email'], 'password'=>$payload['password']]);
        $login->assertOk()->assertJsonStructure(['token']);

        return $login->json('token');
    }

    public function test_products_can_be_created_and_listed()
    {
        $products = Product::factory(3)->create();
        $this->getJson('/api/v1/products')->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_add_update_view_cart()
    {
        $user = User::factory()->create(['password'=>Hash::make('secret123')]);
        $product = Product::factory()->create(['stock'=>10]);
        $token = $user->createToken('t')->plainTextToken;
        $headers = ['Authorization'=>"Bearer $token"];

        // Add to cart
        $add = $this->postJson('/api/v1/cart/items', ['product_id'=>$product->id,'quantity'=>2], $headers);
        $add->assertCreated()->assertJsonStructure(['data'=>['id','product_id','quantity']]);

        // View cart
        $cart = $this->getJson('/api/v1/cart', $headers);
        $cart->assertOk()->assertJsonStructure(['data'=>['items']]);

        // Update cart item
        $cartItemId = $add->json('data.id');
        $this->patchJson("/api/v1/cart/items/$cartItemId", ['quantity'=>3], $headers)
            ->assertOk()->assertJson(['data'=>['quantity'=>3]]);
    }

    public function test_checkout_confirms_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock'=>5]);
        $token = $user->createToken('t')->plainTextToken;
        $headers = ['Authorization'=>"Bearer $token"];

        $this->postJson('/api/v1/cart/items', ['product_id'=>$product->id,'quantity'=>2], $headers);

        $orderResp = $this->postJson('/api/v1/checkout/confirm', [
            'shipping_address'=>['line1'=>'Rua A','city'=>'Maputo','province'=>'MP','country'=>'MZ']
        ], $headers);

        $orderResp->assertOk()->assertJsonStructure(['data' => ['id', 'status', 'total']]);
    }

    public function test_payment_init_and_webhook_capture()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock'=>5]);
        $token = $user->createToken('t')->plainTextToken;
        $headers = ['Authorization'=>"Bearer $token"];

        $this->postJson('/api/v1/cart/items', ['product_id'=>$product->id,'quantity'=>1], $headers);

        $orderResp = $this->postJson('/api/v1/checkout/confirm', [
            'shipping_address'=>['line1'=>'Rua A','city'=>'Maputo','province'=>'MP','country'=>'MZ']
        ], $headers);

        $orderId = $orderResp->json('data.id');

        $paymentResp = $this->postJson("/api/v1/orders/$orderId/payments/init", ['provider'=>'mpesa'], $headers);
        $paymentResp->assertCreated()->assertJsonStructure(['payment'=>['id','provider','status']]);

        $paymentId = $paymentResp->json('payment.id');

        // Simulate webhook capture
        $this->postJson("/api/v1/payments/webhook/mpesa", ['payment_id'=>$paymentId,'status'=>'captured'])
            ->assertOk()->assertJson(['message'=>'OK']);

        $order = Order::find($orderId);
        $this->assertEquals('paid', $order->status);
    }
}
