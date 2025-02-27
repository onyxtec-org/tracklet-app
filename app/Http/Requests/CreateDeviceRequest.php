<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'device_name' => 'required|string|max:255',
            'versions' => 'required|array|min:1',
            'versions.*.version_name' => 'required|string|max:100',
            'versions.*.colors.*' => 'required|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'device_name.required' => 'The device name is required.',
            'device_name.string' => 'The device name must be a string.',
            'device_name.max' => 'The device name cannot exceed 255 characters.',

            'versions.required' => 'At least one device version is required.',
            'versions.array' => 'Invalid data format for versions.',
            'versions.min' => 'You must provide at least one version.',

            'versions.*.version_name.required' => 'Each version must have a name.',
            'versions.*.version_name.string' => 'Version name must be a valid string.',
            'versions.*.version_name.max' => 'Version name cannot exceed 100 characters.',

            'versions.*.colors.array' => 'Colors must be an array.',
            'versions.*.colors.*.required' => 'Each version must have at least one color.',
            'versions.*.colors.*.string' => 'Color name must be a valid string.',
            'versions.*.colors.*.max' => 'Color name cannot exceed 50 characters.',
        ];
    }
}

