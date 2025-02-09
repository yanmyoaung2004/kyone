<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'payment_status' => $this->payment_status,
            'products' => ProductCollection::make($this->whenLoaded('products')),
        ];
    }
}
