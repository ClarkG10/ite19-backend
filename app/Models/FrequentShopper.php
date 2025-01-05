<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequentShopper extends Model
{
    use HasFactory;

    protected $table = 'frequent_shopper';

    protected $fillable = [
        'is_frequent_shopper', // boolean
        'customer_id',
        'store_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function store()
    {
        return $this->belongsTo(User::class, 'store_id');
    }
}
