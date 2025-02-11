<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn  extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'reason',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationship: Belongs to an Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship: Belongs to an Complaint
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    // Relationship: Belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
