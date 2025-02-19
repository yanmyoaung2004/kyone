<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'discount_title',
        'type',
        'product_id',
        'discount',
        'lock',
        'start',
        'end',
    ];

    /**
     * Cast attributes to proper data types.
     */
    protected $casts = [
        'lock' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * Relationship: Discount belongs to a Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
