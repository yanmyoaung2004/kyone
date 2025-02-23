<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $status;

    /**
     * Create a new message instance.
     *
     * @param $order
     * @param $status
     * @return void
     */
    public function __construct($order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.orders.status_updated')
            ->with([
                'orderId' => $this->order->id,
                'orderStatus' => $this->order->status,
                'orderTotal' => $this->order->total_price,
                'orderETA' => $this->order->eta,
            ]);
    }
}
