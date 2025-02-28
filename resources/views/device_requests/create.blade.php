@extends('layouts.contentLayoutMaster')

@section('title', 'Device Request')

@section('content')
@include('panels.response')
<div class="card">

    <div class="card-header">
        <h4 class="card-title">Device Request Form</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('device.request.store') }}" method="POST">
            @csrf
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Name</label>
                    <input type="text" name="name" class="form-control" required>
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Email</label>
                    <input type="email" name="email" class="form-control" required>
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" required>
                    @error('phone_number')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Select Device -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Select Device</label>
                    <select id="device" name="device_id" class="form-control" required>
                        <option value="">Select Device</option>
                        @foreach ($devices as $device)
                        <option value="{{ $device->id }}" data-versions="{{ json_encode($device->versions) }}">
                            {{ $device->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('device_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Device Version (Initially Hidden) -->
                <div class="col-md-6 mb-3 d-none" id="device-version-container">
                    <label class="form-label fw-bold d-block">Device Version</label>
                    <select id="device_version" name="device_version_id" class="form-control" required>
                        <option value="">Select Version</option>
                    </select>
                    @error('device_version_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Primary Color (Initially Hidden) -->
                <div class="col-md-6 mb-3 d-none" id="primary-color-container">
                    <label class="form-label fw-bold d-block">Primary Color</label>
                    <select id="primary_color" name="primary_color_id" class="form-control" required>
                        <option value="">Select Primary Color</option>
                    </select>
                    @error('primary_color_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Secondary Color (Initially Hidden) -->
                <div class="col-md-6 mb-3 d-none" id="secondary-color-container">
                    <label class="form-label fw-bold d-block">Secondary Color</label>
                    <select id="secondary_color" name="secondary_color_id" class="form-control" required>
                        <option value="">Select Secondary Color</option>
                    </select>
                    @error('secondary_color_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Shipping Address -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Shipping Address</label>
                    <select name="shipping_address_id" class="form-control">
                        <option value="">Select Shipping Address</option>
                        @foreach ($shippingAddresses as $address)
                        <option value="{{ $address->id }}">{{ $address->address }}</option>
                        @endforeach
                    </select>
                    @error('shipping_address_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Shipping Attention</label>
                    <input type="text" name="shipping_attention" class="form-control">
                    @error('shipping_attention')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Caller ID Requested</label>
                    <input type="text" name="caller_id_requested" class="form-control">
                    @error('caller_id_requested')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@section('page-script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deviceSelect = document.getElementById('device');
        const deviceVersionSelect = document.getElementById('device_version');
        const primaryColorSelect = document.getElementById('primary_color');
        const secondaryColorSelect = document.getElementById('secondary_color');

        const deviceVersionContainer = document.getElementById('device-version-container');
        const primaryColorContainer = document.getElementById('primary-color-container');
        const secondaryColorContainer = document.getElementById('secondary-color-container');

        // When a device is selected, show versions
        deviceSelect.addEventListener('change', function() {
            let selectedDevice = this.options[this.selectedIndex];
            let versions = JSON.parse(selectedDevice.getAttribute('data-versions') || '[]');

            deviceVersionSelect.innerHTML = '<option value="">Select Version</option>';
            primaryColorSelect.innerHTML = '<option value="">Select Primary Color</option>';
            secondaryColorSelect.innerHTML = '<option value="">Select Secondary Color</option>';

            if (versions.length > 0) {
                versions.forEach(version => {
                    deviceVersionSelect.innerHTML += `<option value="${version.id}" data-colors='${JSON.stringify(version.colors)}'>${version.version}</option>`;
                });
                deviceVersionContainer.classList.remove('d-none');
            } else {
                deviceVersionContainer.classList.add('d-none');
                primaryColorContainer.classList.add('d-none');
                secondaryColorContainer.classList.add('d-none');
            }
        });

        // When a device version is selected, show colors
        deviceVersionSelect.addEventListener('change', function() {
            let selectedVersion = this.options[this.selectedIndex];
            let colors = JSON.parse(selectedVersion.getAttribute('data-colors') || '[]');

            primaryColorSelect.innerHTML = '<option value="">Select Primary Color</option>';
            secondaryColorSelect.innerHTML = '<option value="">Select Secondary Color</option>';

            if (colors.length > 0) {
                colors.forEach(color => {
                    primaryColorSelect.innerHTML += `<option value="${color.id}">${color.color_name}</option>`;
                    secondaryColorSelect.innerHTML += `<option value="${color.id}">${color.color_name}</option>`;
                });
                primaryColorContainer.classList.remove('d-none');
                secondaryColorContainer.classList.remove('d-none');
            } else {
                primaryColorContainer.classList.add('d-none');
                secondaryColorContainer.classList.add('d-none');
            }
        });

        // Hide the selected primary color in the secondary color dropdown
        primaryColorSelect.addEventListener('change', function() {
            let selectedPrimaryColor = this.value;

            for (let option of secondaryColorSelect.options) {
                option.hidden = false;
                if (option.value === selectedPrimaryColor) {
                    option.hidden = true;
                }
            }
        });
    });
</script>
@endsection