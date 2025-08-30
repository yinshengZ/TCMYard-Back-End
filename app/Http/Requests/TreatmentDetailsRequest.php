<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TreatmentDetailsRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //'inventory_id' => 'required|integer',
            'service_id' => 'required|integer',
            'sku_usage' => 'required|array',
            'sku_usage.*.sku_id' => 'required|integer',
            'sku_usage.*.description' => 'string|nullable',
            'sku_usage.*.used_units' => 'required|integer',
            'sku_usage.*.inventory_id' => 'integer|integer',
            'quantity' => 'required|integer',
            'patient_id' => 'required|integer',
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'discount' => 'required|integer|min:0|max:100'
        ];
    }

    public function messages(): array
    {
        return [
            'inventory_id.required' => 'Inventory ID is missing!',
            'inventory_id.integer' => 'Inventory ID must be an integer',
            'service_id.integer' => 'Service ID must be an integer',
            'service_id.required' => 'Please choose a service type!',
            'sku_usage.required' => 'A product is required',

            'sku_usage.sku_id.integer' => 'Product ID must be an integer',

            'sku_usage.sku_id.required' => 'Please select a product',
            'sku_usage.used_units.required' => 'Amount of units used is required',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be a number',
            'patient_id.required' => 'Patient ID is missing!',
            'patient_id.integer' => 'Patient ID must be an integer',
            'user_id.integer' => 'User ID must be an integer',
            'user_id.required' => 'User ID is missing!',
            'date.required' => 'Please provide the date of the treatment',
            'discount' => 'Discount must be in the range of 0-100'
        ];
    }
}
