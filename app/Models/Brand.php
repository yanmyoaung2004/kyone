<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    // Define which fields are mass-assignable (if necessary)
    protected $fillable = ['name'];

    // Define the relationship to the Product model
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
