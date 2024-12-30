<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'store_id', 'is_ordered'];

    // Relationship with CartItems
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
