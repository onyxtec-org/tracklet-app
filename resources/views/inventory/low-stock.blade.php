@extends('layouts.contentLayoutMaster')

@section('title', 'Low Stock Items')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Low Stock Items</h4>
                <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-1"></i> Back to Inventory
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-warning border-left-3 border-left-warning shadow-sm">
                    <div class="alert-body">
                        <div class="d-flex align-items-start">
                            <i data-feather="alert-triangle" class="font-medium-3 mr-2 mt-25"></i>
                            <div>
                                <h6 class="alert-heading mb-1 font-weight-bolder">Low Stock Warning</h6>
                                <p class="mb-0">The following items are below their minimum threshold and may need to be restocked.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Minimum Threshold</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->category ?? '-' }}</td>
                                <td><strong class="text-danger">{{ $item->quantity }}</strong></td>
                                <td>{{ $item->minimum_threshold }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>
                                    <span class="badge badge-light-warning">Low Stock</span>
                                </td>
                                <td>
                                    <a href="{{ route('inventory.items.show', $item) }}" class="btn btn-sm btn-primary">
                                        <i data-feather="eye" class="mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No low stock items found. Great job!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
$(function() {
    if (feather) {
        feather.replace({ width: 14, height: 14 });
    }
});
</script>
@endsection

