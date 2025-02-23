<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /* Create a new event instance.
     */
    public $notification;
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /* Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info("reach");
        return [
            new Channel("public-updates"),
        ];
    }

    public function broadcastWith(): array
    {
        return array(
            'message' => $this->notification,
        );
    }

    public function broadcastAs(): string
    {
        return 'public.notification';
    }
}
