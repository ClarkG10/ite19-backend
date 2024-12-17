<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'product_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UPC', //automatic nani generated pwede ra wala
        'product_name',
        'product_type',
        'brand',
        'description',
        'image_path',
        'is_active', // naa default value pwede ra wala
        'vendor_id', // id sa vendor nga nacreate nga acc and mao ni tag iya sa isa ka product pwede mag balik2
        'section_name', // kadtong grocery, electronics or unsa pa
        'status', //pwede ra wala kay naa ni default value
        'selling_price',
        'cost_price',
        'wholesale_price',
        'stock_quantity',
    ];

    public function reorderRequests()
    {
        return $this->hasMany(ReorderRequest::class, 'product_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_id');
    }
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['product_id', 'created_at', 'updated_at'];
}
