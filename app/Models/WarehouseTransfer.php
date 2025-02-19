<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'product_id',
        'quantity',
    ];

    /**
     * Relationship: The warehouse this transfer is coming from.
     */
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * Relationship: The warehouse this transfer is going to.
     */
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * Relationship: The product being transferred.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
