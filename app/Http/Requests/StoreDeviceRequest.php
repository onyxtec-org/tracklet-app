<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'device_id' => 'required|exists:devices,id',
            'device_version_id' => 'required|exists:device_versions,id',
            'primary_color_id' => 'required|exists:colors,id',
            'secondary_color_id' => 'nullable|exists:colors,id|different:primary_color_id',
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'shipping_attention' => 'required|string|max:255',
            'caller_id_requested' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The Name is required.',
            'email.required' => 'The Email is required.',
            'email.email' => 'Enter a valid address.',
            'phone_number.required' => 'The Phone Number is required.',
            'device_id.required' => 'Please select a Device.',
            'device_version_id.required' => 'Please select a Device Version.',
            'primary_color_id.required' => 'Please select a Primary Color.',
            'secondary_color_id.different' => 'Secondary Color must be different from Primary Color.',
            'shipping_address_id.required' => 'Please select a Shipping Address.',
            'shipping_attention.required' => 'Please enter any shipping attentions.',
        ];
    }
}
