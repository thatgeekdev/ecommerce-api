<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','status','subtotal','shipping_total','tax_total','total','currency','shipping_address','billing_address'];
    
    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function items() 
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments() 
    {
        return $this->hasMany(Payment::class);
    }
}
