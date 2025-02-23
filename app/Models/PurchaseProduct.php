<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    protected $table = 'purchase_products';

    protected $fillable = [
        'product_id',
        'quantity',
        'purchase_id',
        'purchase_price_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function purchasePrice()
    {
        return $this->belongsTo(PurchasePrice::class, 'purchase_price_id');
    }
}