<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';

    protected $fillable = [
        'service_center_id',
        'invoice_number',
    ];

        public function serviceCenter()
    {
        return $this->belongsTo(ServiceCenter::class);
    }
}