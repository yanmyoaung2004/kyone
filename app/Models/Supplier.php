<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'credit_limit',
    ];


    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
