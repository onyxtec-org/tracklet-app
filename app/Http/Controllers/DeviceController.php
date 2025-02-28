<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceVersion;
use App\Models\Color;
use App\Http\Requests\CreateDeviceRequest;

class DeviceController extends Controller
{
    public function create()
    {
        return view('devices.create');
    }

    public function store(CreateDeviceRequest $request)
    {
        $validatedData = $request->validated();

        if (!isset($validatedData['versions']) || empty($validatedData['versions'])) {
            return redirect()->back()->withErrors(['versions' => 'At least one version is required.']);
        }

        $device = Device::create(['name' => $validatedData['device_name']]);

        foreach ($validatedData['versions'] as $index => $version) {
            if (!isset($version['version_name']) || empty($version['version_name'])) {
                continue;
            }

            $deviceVersion = DeviceVersion::create([
                'device_id' => $device->id,
                'version' => $version['version_name']
            ]);

            if (isset($version['colors']) && is_array($version['colors'])) {
                foreach ($version['colors'] as $color) {
                    if (!empty($color)) {
                        Color::create([
                            'device_id' => $device->id,
                            'device_version_id' => $deviceVersion->id,
                            'color_name' => trim($color),
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Device, versions, and colors added successfully!');
    }

    public function list()
    {
        if (request()->ajax()) {
            $devices = Device::with(['versions.colors'])->get();
            return ['data' => $devices];
        }
        return view('devices.list');
    }
}
