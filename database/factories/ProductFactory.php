<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name.'-'.uniqid()),
            'description' => $this->faker->sentence(12),
            'price' => $this->faker->randomFloat(2, 100, 10000),
            'stock' => $this->faker->numberBetween(0, 200),
            'is_active' => true,
        ];
    }
}