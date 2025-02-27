<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\ShippingAddress;
use App\Models\DeviceRequest;
use App\Http\Requests\StoreDeviceRequest;
use App\Mail\DeviceRequestSubmitted;
use Illuminate\Support\Facades\Mail;

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
        $deviceRequest = DeviceRequest::create($request->validated());
        Mail::to($deviceRequest->email)->queue(new DeviceRequestSubmitted($deviceRequest));
        return redirect()->back()->with('success', 'Device request submitted successfully!');
    }

    public function list()
    {
        if (request()->ajax()) {
            $deviceRequests = DeviceRequest::with(['device', 'deviceVersion', 'primaryColor', 'secondaryColor', 'shippingAddress'])->get();
            return ['data' => $deviceRequests];
        }
        return view('device_requests.list');
    }
}
