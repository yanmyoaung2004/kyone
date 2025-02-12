<?php

namespace App\Http\Controllers;

use App\Events\UserEvent;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        try{
            $notification = Notification::all();
            return response()->json($notification);
           }catch(Exception $e){
            return response()->json(['message'=>"Failed to get notification",'error'=>$e->getMessage()]);
           }
    }
    public function create(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'resource_id' => 'required|integer',
                'type' => 'required|string|in:truck,order,driver,customer,stock,product',
                'role' => 'required|string|in:customer,warehouse,driver,sale',
                'message' => 'required|string',
            ]);

            // Create notification
            $notification = Notification::create([
                'resource_id' => $validated['resource_id'],
                'type' => $validated['type'],
                'role' => $validated['role'],
                'message' => $validated['message'],
            ]);

            // Broadcast the event
            broadcast(new UserEvent($notification))->toOthers();

            return response()->json(["message" => "Notification sent", "notification" => $notification], 201);
        } catch (Exception $e) {
            return response()->json(['message' => "Notification send failed", 'error' => $e->getMessage()], 500);
        }
    }
}
