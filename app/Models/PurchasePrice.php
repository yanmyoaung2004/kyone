<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePrice extends Model
{
    protected $table = 'purchase_prices';

    protected $fillable = [
        'product_id',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
