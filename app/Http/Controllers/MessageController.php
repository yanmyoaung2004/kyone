<?php

namespace App\Http\Controllers;

use App\Events\CustomerMessageSent;
use App\Events\MessageSent;
use App\Events\SentMessage;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function create(Request $request)
{
    $validated = $request->validate([
        'sender_id' => 'required',
        'receiver_id' => 'nullable',
        'message' => 'required|string',
        'role' => 'required|in:warehouse,sale,customer', //role is receiver role
    ]);
    $message = Message::create([
        'sender_id' => $validated['sender_id'],
        'receiver_id' => $validated['receiver_id'] ?? null,
        'message' => $validated['message'],
        'role' => $validated['role']
    ]);
    broadcast(new MessageSent($message));

    return response()->json([
        'message' => 'Message sent successfully',
        'data' => $message
    ], 201);
}

public function customerMessage($customer_id){
    $messages = Message::where('sender_id', $customer_id)
                   ->orWhere('receiver_id', $customer_id)
                   ->get();
    return response()->json([
        'message' => "Message get successfully",
        'data' => $messages,
    ]);
}

public function saleMessage($receiver_id){
    $messages = Message::where('receiver_id', $receiver_id)
                ->orWhere('sender_id', $receiver_id)
                ->get();
    return response()->json([
        'message' => "Message get for sale message successfully",
        'data' => $messages,
    ]);
}
}
