@extends('layouts.contentLayoutMaster')

@section('title', 'Maintenance Records')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Maintenance Records</h4>
                <div>
                    @if($upcoming_count > 0)
                        <a href="{{ route('maintenance.upcoming') }}" class="btn btn-warning mr-1">
                            <i data-feather="calendar" class="mr-1"></i> Upcoming ({{ $upcoming_count }})
                        </a>
                    @endif
                    <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="mr-1"></i> Add Record
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ route('maintenance.index') }}" class="mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="scheduled" {{ request('type') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="repair" {{ request('type') == 'repair' ? 'selected' : '' }}>Repair</option>
                                <option value="inspection" {{ request('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Asset</label>
                            <select name="asset_id" class="form-control">
                                <option value="">All Assets</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Scheduled Date</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                            <tr>
                                <td>
                                    @if($record->asset)
                                        <strong>{{ $record->asset->name }}</strong><br><small class="text-muted">{{ $record->asset->asset_code }}</small>
                                    @else
                                        <strong class="text-muted">N/A (Asset Deleted)</strong>
                                    @endif
                                </td>
                                <td><span class="badge badge-light-primary">{{ ucfirst($record->type) }}</span></td>
                                <td>{{ $record->scheduled_date->format('M d, Y') }}</td>
                                <td>
                                    @if($record->status == 'pending')
                                        <span class="badge badge-light-warning">Pending</span>
                                    @elseif($record->status == 'in_progress')
                                        <span class="badge badge-light-info">In Progress</span>
                                    @elseif($record->status == 'completed')
                                        <span class="badge badge-light-success">Completed</span>
                                    @else
                                        <span class="badge badge-light-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($record->description, 50) }}</td>
                                <td>{{ $record->cost ? '$' . number_format($record->cost, 2) : '-' }}</td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="{{ route('maintenance.show', $record) }}" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <a href="{{ route('maintenance.edit', $record) }}" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="{{ route('maintenance.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No maintenance records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    {{ $records->links() }}
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



