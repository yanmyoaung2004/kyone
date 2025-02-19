<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
    ];

    /**
     * Relationship: WarehouseProduct belongs to a Warehouse.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Relationship: WarehouseProduct belongs to a Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
