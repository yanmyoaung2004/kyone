<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['integer', 'exists:customers,id'],
            'location_id' => ['integer', 'exists:locations,id'],
            'status' => ['in:pending,inprogress,delivered,cancelled,delayed'],
            'total_price' => ['string'],
            'eta' => ['string'],
            'payment_status' => ['in:pending,paid,failed'],
        ];
    }
}
