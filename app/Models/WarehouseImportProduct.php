<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseImportProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_product_id',
        'quantity',
    ];

    /**
     * Relationship: WarehouseImportProduct belongs to a PurchaseProduct.
     */
    public function purchaseProduct()
    {
        return $this->belongsTo(PurchaseProduct::class);
    }
}
