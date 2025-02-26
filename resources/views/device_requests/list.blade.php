@extends('layouts.contentLayoutMaster')

@section('title', 'Device Requests')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Device Requests</h4>
    </div>
    <div class="card-body">
        <table class="table device-requests-table" id="device-requests-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Device</th>
                    <th>Version</th>
                    <th>Primary Color</th>
                    <th>Secondary Color</th>
                    <th>Shipping Address</th>
                    <th>Shipping Attention</th>
                    <th>Caller ID Requested</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deviceRequests as $request)
                <tr>
                    <td>{{ $request->name }}</td>
                    <td>{{ $request->email }}</td>
                    <td>{{ $request->phone_number }}</td>
                    <td>{{ $request->device->name ?? '-' }}</td>
                    <td>{{ $request->deviceVersion->version ?? '-' }}</td>
                    <td>{{ $request->primaryColor->color_name ?? '-' }}</td>
                    <td>{{ $request->secondaryColor->color_name ?? '-' }}</td>
                    <td>{{ $request->shippingAddress->address ?? '-' }}</td>
                    <td>{{ $request->shipping_attention ?? '-' }}</td>
                    <td>{{ $request->caller_id_requested ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let table = new DataTable('#device-requests-table');
    });
</script>
@endsection