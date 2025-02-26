<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\ShippingAddress;
use App\Models\DeviceRequest;
use App\Http\Requests\StoreDeviceRequest;

class DeviceRequestController extends Controller
{
    public function index()
    {
        $devices = Device::with('versions.colors')->get();
        $shippingAddresses = ShippingAddress::all();

        return view('device_requests.create', compact('devices', 'shippingAddresses'));
    }

    public function store(StoreDeviceRequest $request)
    {
        DeviceRequest::create($request->validated());

        return redirect()->back()->with('success', 'Device request submitted successfully!');
    }

    public function list()
    {
        $deviceRequests = DeviceRequest::with(['device', 'deviceVersion', 'primaryColor', 'secondaryColor', 'shippingAddress'])->get();
        return view('device_requests.list', compact('deviceRequests'));
    }
}
