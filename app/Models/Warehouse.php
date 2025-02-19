<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    /**
     * Define relationships (if any).
     * Example: A warehouse has many products.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Add other relationships or methods if necessary.
     */
}
