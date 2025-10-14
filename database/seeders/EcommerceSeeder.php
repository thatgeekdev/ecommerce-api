<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductImage;

class EcommerceSeeder extends Seeder
{

    public function run(): void
    {
        // Criar categorias
        $categories = [
            'Eletrônicos',
            'Roupas',
            'Livros',
            'Esportes',
        ];

        foreach ($categories as $catName) {
            $category = Categories::create([
                'name' => $catName,
                'slug' => Str::slug($catName),
            ]);

            // Criar 5 produtos por categoria
            for ($i = 1; $i <= 5; $i++) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'sku' => strtoupper(Str::random(8)),
                    'name' => $catName . " Produto $i",
                    'slug' => Str::slug($catName . " Produto $i"),
                    'description' => "Descrição do produto $i da categoria $catName",
                    'price' => rand(100, 5000) / 100,
                    'stock' => rand(1, 50),
                    'is_active' => true,
                ]);

                // Criar imagens
                for ($j = 1; $j <= 2; $j++) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => "https://via.placeholder.com/600x400?text=" . urlencode($product->name) . "+$j",
                        'alt' => $product->name . " Imagem $j",
                        'position' => $j,
                    ]);
                }
            }
        }
    }
}
