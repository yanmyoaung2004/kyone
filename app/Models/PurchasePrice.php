<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePrice extends Model
{

    protected $fillable = ['price', 'product_id'];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}