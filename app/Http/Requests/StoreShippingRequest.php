<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'addresses' => 'required|array|min:1',
            'addresses.*' => 'required|string|max:255|distinct',
        ];
    }

    public function messages()
    {
        return [
            'addresses.required' => 'At least one shipping address is required.',
            'addresses.array' => 'Invalid format for addresses.',
            'addresses.min' => 'You must provide at least one shipping address.',
            'addresses.*.required' => 'Each address field is required.',
            'addresses.*.string' => 'Address must be a valid string.',
            'addresses.*.max' => 'Address cannot exceed 255 characters.',
            'addresses.*.distinct' => 'Duplicate addresses are not allowed.',
        ];
    }
}

