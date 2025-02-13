<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OrderShipped;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendOrderShippedEmail()
    {
        // $order = Order::find($order); // Get your order data

        Mail::to('test@gmail.com')->send(new OrderStatusUpdated((object)['name' => '123', 'total' => '123'], 'shipped'));
        return 1;
    }
}
