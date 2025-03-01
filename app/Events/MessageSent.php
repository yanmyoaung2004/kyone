<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        if($this->message->role !== "customer"){
            Log::info("message role: ".$this->message->role);
            return new PrivateChannel('role.'.$this->message->role);
        }
        Log::info("message role in chat: ".$this->message->role);
        return new PrivateChannel('chat.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
