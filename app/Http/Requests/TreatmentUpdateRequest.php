<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TreatmentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required',
            'service_id' => 'required',
            'user_id' => 'required',
            'quantity' => 'required',
            'discount' => 'required',
            'inventories' => 'required|array',

        ];
    }

    public function messages()
    {
        return [
            'quantity.required' => "Quantity is required to be at least 1",
            'discount.required' => "Discount is at least 0%",
            'inventories.required' => "You need to add least one item to update this treatment!",
        ];
    }
}
