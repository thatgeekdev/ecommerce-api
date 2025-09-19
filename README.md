# E-commerce API (Laravel 12)

API RESTful for e-commerce, built with **Laravel 12**, ready to be consumed by **web frontends** (Next.js/Nuxt.js) or **mobile apps** (React Native/Flutter).

Includes authentication, products, cart, orders, and base build prototype for payment integrations (MPesa, e-Mola, Stripe, PayPal, Manual).

---

## <tags>Technologies</tags>

* PHP 8.2 / Laravel 12
* MySQL / MariaDB
* Sanctum (API Token Auth)
* Spatie Permission (RBAC)
* Redis (optional caching)
* PHPUnit / Pest (tests)
* Postman / Insomnia (API testing)
* CORS & Rate-limiting

---

## <tags>Setup</tags>

```bash
git clone <repo-url> ecommerce-api
cd ecommerce-api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### <tags>Environment Variables</tags>

```env
APP_NAME=EcommerceAPI
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=

CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:3000
ADMIN_EMAILS=admin@exemplo.com

MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
EMOLA_API_KEY=
STRIPE_KEY=
STRIPE_SECRET=
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=
```

---

## <tags>Authentication</tags>

### Register

```http
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "Jose",
    "email": "jose@example.com",
    "password": "secret123"
}
```

**Response:**

```json
{
  "token": "<sanctum-token>"
}
```

### Login

```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "jose@example.com",
    "password": "secret123"
}
```

**Response:**

```json
{
  "token": "<sanctum-token>"
}
```

---

## <tags>Cart</tags>

Requires **Bearer Token**.

* List items: `GET /api/v1/cart`
* Add item: `POST /api/v1/cart/items`

```json
{
  "product_id": 1,
  "quantity": 2
}
```

* Update item: `PATCH /api/v1/cart/items/{id}`

```json
{
  "quantity": 3
}
```

* Remove item: `DELETE /api/v1/cart/items/{id}`

---

## <tags>Products</tags>

* List active products: `GET /api/v1/products`
* View product details: `GET /api/v1/products/{slug}`

**Admin / Product Manager:** create, update, delete products using **Spatie RBAC**.

```php
Route::middleware('can:manage-products')->group(function () {
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
});
```

---

## <tags>Orders</tags>

* Confirm checkout / create order: `POST /api/v1/checkout/confirm`

```json
{
  "shipping_address": {
    "line1": "Rua A",
    "city": "Maputo",
    "province": "MP",
    "country": "MZ"
  }
}
```

* List orders: `GET /api/v1/orders`
* Order detail: `GET /api/v1/orders/{order}`

> Only the order owner can view/update.

---

## <tags>Payments</tags>

* Initiate payment: `POST /api/v1/orders/{order}/payments/init`

```json
{
  "provider": "mpesa"
}
```

* Public webhook: `POST /api/v1/payments/webhook/{provider}`

  * HMAC validation
  * Idempotency
  * Automatically updates order status

---

## <tags>Models & Relationships</tags>

* **User**
* **Product**
* **Cart**
* **CartItem**
* **Order**
* **OrderItem**
* **Payment**

### Example: Cart → CartItems

```php
$cart = Cart::with('items.product')->where('user_id', $userId)->first();
$total = $cart->items->sum(fn($item) => $item->total);
```

---

## <tags>Best Practices</tags>

* Spatie RBAC with Gates and Policies
* Middleware to force **JSON responses**
* API Rate Limit (60 requests/min)
* Configurable CORS
* Factories & Seeders for testing
* Feature Tests included (Auth, Products, Cart/Order flow)
* HTTPS, HSTS, CSP headers
* Queue for emails / payment webhooks

---

## <tags>Testing</tags>

```bash
php artisan test
```

Feature test example:

```php
it('complete flow: add cart -> confirm order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock'=>5]);

    $token = $user->createToken('t')->plainTextToken;
    $headers = ['Authorization' => 'Bearer '.$token];

    $this->postJson('/api/v1/cart/items', ['product_id'=>$product->id,'quantity'=>2], $headers)->assertCreated();

    $orderResp = $this->postJson('/api/v1/checkout/confirm', [
        'shipping_address' => ['line1' => 'Rua A', 'city' => 'Maputo', 'province' => 'MP', 'country' => 'MZ']
    ], $headers)->assertOk();

    expect($orderResp->json('data.id'))->toBeInt();
});
```

---

## <tags>Production Recommendations</tags>

* Use **HTTPS / TLS**
* Protect webhooks with **HMAC + idempotency**
* Structured JSON logs + Sentry
* Automatic DB + storage backups
* Redis caching for catalog + CDN
* Scale queues for emails and payments

> With these components, the API is **ready for consumption** by web and mobile clients, maintaining **stable contracts** and following **secure clean code practices**.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
