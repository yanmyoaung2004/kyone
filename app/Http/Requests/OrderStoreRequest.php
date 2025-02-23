<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            //'customer_id' => 'required|exists:customers,id',
            'customer_id' => 'required',
            'shipmentInfo.address' => 'required|string|max:255',
            'shipmentInfo.city' => 'required|string|max:100',
            'total' => 'required|numeric|min:0',
            'payment' => 'required|string|in:credit_card,paypal,cash,bank_transfer',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.unitprice_id' => 'required|exists:unitprices,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
