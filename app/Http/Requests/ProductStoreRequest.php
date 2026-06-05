<?php
// app/Http/Requests/ProductStoreRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'size_id' => 'required|exists:sizes,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ];
    }
    
    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category',
            'category_id.exists' => 'Selected category does not exist',
            'size_id.required' => 'Please select a size',
            'size_id.exists' => 'Selected size does not exist',
            'name.required' => 'Product name is required',
            'name.max' => 'Product name cannot exceed 255 characters',
            'price.required' => 'Product price is required',
            'price.min' => 'Price cannot be negative',
            'quantity.required' => 'Product quantity is required',
            'quantity.min' => 'Quantity cannot be negative',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}