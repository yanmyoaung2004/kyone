<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalatedIssue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
        protected $fillable = [
        'description',
        'driver_id',
        'status',
        'route_key',
        'priority',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'driver_id' => 'integer',
    ];

    public function getCity()
    {
        $orderAssignTruck = OrderAssignTruck::where('route_key', $this->route_key)->first();

        if ($orderAssignTruck && $orderAssignTruck->order) {
            return $orderAssignTruck->order->location->city->name;
        }
        return null;
    }

    public function getTruck()
    {
        $orderAssignTruck = OrderAssignTruck::where('route_key', $this->route_key)->first();

        if ($orderAssignTruck && $orderAssignTruck->order) {
            return $orderAssignTruck->truck->license_plate;
        }
        return null;
    }


    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}