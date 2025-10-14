<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\{Product, ProductImage};
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('is_active', true)
            ->with(['images','category'])
            ->when($request->q, fn($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->when($request->category_id, fn($q,$id) => $q->where('category_id', $id))
            ->orderBy($request->get('sort','created_at'), $request->get('dir','desc'));

        return ProductResource::collection($query->paginate($request->get('per_page', 12)));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->with(['images','category'])->firstOrFail();
        return new ProductResource($product);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->safe()->except('images'));
        foreach (($request->images ?? []) as $i => $img) {
            ProductImage::create([
                'product_id' => $product->id,
                'url' => $img['url'],
                'alt' => $img['alt'] ?? null,
                'position' => $i + 1,
            ]);
        }
        return new ProductResource($product->load(['images','category']));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->safe()->except('images'));
        if ($request->filled('images')) {
            $product->images()->delete();
            foreach ($request->images as $i => $img) {
                $product->images()->create([
                    'url' => $img['url'],
                    'alt' => $img['alt'] ?? null,
                    'position' => $i + 1,
                ]);
            }
        }
        return new ProductResource($product->load(['images','category']));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Produto removido']);
    }
}