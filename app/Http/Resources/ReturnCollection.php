<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReturnCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($orderReturn) {
                return [
                    'id' => $orderReturn->id,
                    'order_id' => $orderReturn->order_id,
                    'product_id' => $orderReturn->product_id,
                    'quantity' => $orderReturn->quantity,
                    'reason' => $orderReturn->reason,
                    'status' => $orderReturn->status,
                    'created_at' => $orderReturn->created_at,
                    'updated_at' => $orderReturn->updated_at,
                    // Include related complaint data
                    'complaint_id' => $orderReturn->order->complaint->id ?? null,
                    'complaint_status' => $orderReturn->order->complaint->status ?? null,
                ];
            }),
        ];
    }
}
