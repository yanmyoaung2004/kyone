<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAssignTruck extends Model
{
    //
    use HasFactory;

    protected $fillable = ['order_id', 'driver_id', 'truck_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function truck()
    
    {
        return $this->belongsTo(Truck::class);
    }

  

    
}
