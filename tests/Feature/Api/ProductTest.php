<?php
namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lista_produtos_publicos()
    {
        // Cria 5 produtos no banco
        Product::factory(5)->create();

        // Requisição GET para listar produtos
        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonStructure([
                'data', // garante que a resposta tem chave 'data'
            ]);
    }
}
