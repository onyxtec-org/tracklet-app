<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceVersion;
use App\Models\Color;
use App\Models\ShippingAddress;

class DeviceSeeder extends Seeder
{
    public function run()
    {
        $devices = [
            ['name' => 'iPhone'],
            ['name' => 'Samsung Galaxy'],
            ['name' => 'Google Pixel']
        ];

        $addresses = [
            ['address' => '123 Main Street, New York, NY'],
            ['address' => '456 Oak Avenue, Los Angeles, CA'],
            ['address' => '789 Pine Road, Chicago, IL']
        ];

        foreach ($addresses as $address) {
            ShippingAddress::create($address);
        }

        foreach ($devices as $deviceData) {
            $device = Device::create($deviceData);

            $versions = ['Pro', 'Plus', 'Ultra'];
            foreach ($versions as $version) {
                $deviceVersion = DeviceVersion::create([
                    'device_id' => $device->id,
                    'version' => $version
                ]);

                $colors = ['Black', 'White', 'Blue', 'Red'];
                foreach ($colors as $color) {
                    Color::create([
                        'device_id' => $device->id,
                        'device_version_id' => $deviceVersion->id,
                        'color_name' => $color
                    ]);
                }
            }
        }
    }
}

