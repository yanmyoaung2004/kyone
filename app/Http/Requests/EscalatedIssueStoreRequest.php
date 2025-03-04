<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EscalatedIssueStoreRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'order_id' => ['required', 'exists:orders,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'priority' => ['required', 'in:high,low,medium'],
        ];
    }
}
