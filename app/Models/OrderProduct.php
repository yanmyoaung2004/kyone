<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    //
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'unitprice_id',];

    public function product()
{
    return $this->belongsTo(Product::class);
}

}
