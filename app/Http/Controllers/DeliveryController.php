<?php

namespace App\Http\Controllers;

use App\Models\EscalatedIssue;
use App\Models\Order;
use App\Models\OrderAssignTruck;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    //
    public function index(){
        /**
        $data = OrderAssignTruck::select('truck_id')
            ->groupBy('truck_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        */
        $data = OrderAssignTruck::with('order.customer.user', 'order.location', 'driver.user', 'order.invoice')->get();
        $formattedData = $data->map(function ($order){
            return [
                'id' => $order->order->invoice->invoice_number,
                'customer' => $order->order->customer->user->name,
                'address' => $order->order->location->address,
                'eta' => $order->order->eta,
                'status' => $order->order->status,
                'driver' => $order->driver->user->name,
                ];
        });
        $status = $this->getDeliveryCardData();
        $orderByTruck = $this->getDataByTruck();
        return response()->json([
                'order' => $formattedData,
                'status' => $status,
                'orderTruck' => $orderByTruck
            ]);
    }

    public function getDataByTruck()
    {
        $data = OrderAssignTruck::with('order', 'driver.user', 'truck')
            ->get()
            ->groupBy('truck_id')
            ->map(function ($orders) {
                $firstOrder = $orders->first();
                return [
                    'truck_id' => $firstOrder->truck->id,
                    'driver_name' => $firstOrder->driver->user->name ?? null,
                    'truck_name' => $firstOrder->truck->license_plate ?? null,
                    'eta' => $firstOrder->order->eta ?? null,
                    'status' => $firstOrder->order->status ?? null,
                    'route' => 'Myitkyina'
                ];
            })->toArray();

        return array_values($data);
    }




    private function getDeliveryCardData(){
        $activeDelivery = Order::where('status', '=', 'processing')->count();
        $pendingDelivery = Order::where('status', '=', 'pending')->count();
        $escalatedDelivery = EscalatedIssue::count();
        return [
            'activeDelivery' => $activeDelivery,
            'pendingDelivery' => $pendingDelivery,
            'escalatedDelivery' => $escalatedDelivery
        ];
    }

}
