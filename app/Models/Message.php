<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'message'; // Explicitly defining the table

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'role'
    ];

    /**
     * Relationship with sender (User).
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship with receiver (User).
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
