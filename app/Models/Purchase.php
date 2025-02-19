<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';

    protected $fillable = [
        'supplier_id',
        'invoice_number',
    ];

        public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
