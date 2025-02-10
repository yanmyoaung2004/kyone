<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EscalatedIssueUpdateRequest extends FormRequest
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
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'priority' => ['required', 'in:high,low,medium'],
        ];
    }
}
