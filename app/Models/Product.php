<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['category_id','sku','name','slug','description','price','stock','is_active'];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    
        public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
