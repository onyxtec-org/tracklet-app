@extends('layouts.contentLayoutMaster')

@section('title', 'Asset Management')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
@endsection

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Assets</h4>
                @if(!auth()->user()->hasRole('general_staff'))
                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="mr-1"></i> Add Asset
                </a>
                @endif
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="card bg-light-primary">
                            <div class="card-body text-center">
                                <h6 class="mb-0">Total Assets</h6>
                                <h3 class="mb-0">{{ $summary['total'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-success">
                            <div class="card-body text-center">
                                <h6 class="mb-0">Active</h6>
                                <h3 class="mb-0">{{ $summary['active'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-warning">
                            <div class="card-body text-center">
                                <h6 class="mb-0">In Repair</h6>
                                <h3 class="mb-0">{{ $summary['in_repair'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-danger">
                            <div class="card-body text-center">
                                <h6 class="mb-0">Retired</h6>
                                <h3 class="mb-0">{{ $summary['retired'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <form method="GET" action="{{ route(auth()->user()->hasRole('general_staff') ? 'view.assets' : 'assets.index') }}" class="mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="in_repair" {{ request('status') == 'in_repair' ? 'selected' : '' }}>In Repair</option>
                                <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Category</label>
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Assigned To</label>
                            <select name="assigned_to_user_id" class="form-control">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('assigned_to_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search assets...">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="{{ route(auth()->user()->hasRole('general_staff') ? 'view.assets' : 'assets.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asset Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Purchase Date</th>
                                <th>Purchase Price</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                            <tr>
                                <td><strong>{{ $asset->asset_code }}</strong></td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->category }}</td>
                                <td>{{ $asset->purchase_date->format('M d, Y') }}</td>
                                <td>${{ number_format($asset->purchase_price, 2) }}</td>
                                <td>
                                    @if($asset->status == 'active')
                                        <span class="badge badge-light-success">Active</span>
                                    @elseif($asset->status == 'in_repair')
                                        <span class="badge badge-light-warning">In Repair</span>
                                    @else
                                        <span class="badge badge-light-danger">Retired</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->assignedToUser)
                                        {{ $asset->assignedToUser->name }}
                                    @elseif($asset->assigned_to_location)
                                        {{ $asset->assigned_to_location }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="{{ route(auth()->user()->hasRole('general_staff') ? 'view.assets.show' : 'assets.show', $asset) }}" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @if(!auth()->user()->hasRole('general_staff'))
                                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No assets found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    {{ $assets->links() }}
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



