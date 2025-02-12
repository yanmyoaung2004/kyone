<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // Set the table name if it differs from the default (notifications)
    protected $table = 'notification';  // Optional: Laravel defaults to 'notifications'

    // Define the fillable attributes to avoid mass assignment exceptions
    protected $fillable = [
        'resource_id',
        'type',
        'role',
        'message'
    ];

    // Optionally, if you want to define relationships, you can do so here
    // Example: if there's a relationship to a User model, you can define it like this
    /*
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    */
}
