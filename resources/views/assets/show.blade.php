@extends('layouts.contentLayoutMaster')

@section('title', 'Asset Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $asset->name }} - {{ $asset->asset_code }}</h4>
                <div>
                    @if(!auth()->user()->hasRole('general_staff'))
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    @endif
                    <a href="{{ route(auth()->user()->hasRole('general_staff') ? 'view.assets' : 'assets.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Asset Code:</th>
                                <td><strong>{{ $asset->asset_code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td>{{ $asset->name }}</td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td>{{ $asset->category }}</td>
                            </tr>
                            <tr>
                                <th>Purchase Date:</th>
                                <td>{{ $asset->purchase_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Purchase Price:</th>
                                <td><strong class="text-primary">${{ number_format($asset->purchase_price, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Age:</th>
                                <td>{{ $asset->getAgeInDays() }} days ({{ $asset->getAgeInYears() }} years)</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Status:</th>
                                <td>
                                    @if($asset->status == 'active')
                                        <span class="badge badge-light-success">Active</span>
                                    @elseif($asset->status == 'in_repair')
                                        <span class="badge badge-light-warning">In Repair</span>
                                    @else
                                        <span class="badge badge-light-danger">Retired</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Assigned To:</th>
                                <td>
                                    @if($asset->assignedToUser)
                                        {{ $asset->assignedToUser->name }}
                                    @elseif($asset->assigned_to_location)
                                        {{ $asset->assigned_to_location }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Vendor:</th>
                                <td>{{ $asset->vendor ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Warranty Expiry:</th>
                                <td>
                                    @if($asset->warranty_expiry)
                                        {{ $asset->warranty_expiry->format('F d, Y') }}
                                        @if($asset->isWarrantyExpired())
                                            <span class="badge badge-light-danger ml-1">Expired</span>
                                        @else
                                            <span class="badge badge-light-success ml-1">{{ $asset->getWarrantyDaysRemaining() }} days left</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Serial Number:</th>
                                <td>{{ $asset->serial_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Model Number:</th>
                                <td>{{ $asset->model_number ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($asset->description)
                <div class="row mb-2">
                    <div class="col-12">
                        <h6>Description:</h6>
                        <p>{{ $asset->description }}</p>
                    </div>
                </div>
                @endif

                @if($asset->notes)
                <div class="row mb-2">
                    <div class="col-12">
                        <h6>Notes:</h6>
                        <p>{{ $asset->notes }}</p>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>Asset Movements</h5>
                                @if(!auth()->user()->hasRole('general_staff'))
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#movementModal">
                                    <i data-feather="plus" class="mr-1"></i> Log Movement
                                </button>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Reason</th>
                                                <th>Logged By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($asset->movements as $movement)
                                            <tr>
                                                <td>{{ $movement->movement_date->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge badge-light-info">{{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}</span>
                                                </td>
                                                <td>
                                                    @if($movement->fromUser)
                                                        {{ $movement->fromUser->name }}
                                                    @elseif($movement->from_location)
                                                        {{ $movement->from_location }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($movement->toUser)
                                                        {{ $movement->toUser->name }}
                                                    @elseif($movement->to_location)
                                                        {{ $movement->to_location }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $movement->reason ?? '-' }}</td>
                                                <td>{{ $movement->user->name }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No movements recorded.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Maintenance Records</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Scheduled Date</th>
                                                <th>Status</th>
                                                <th>Description</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($asset->maintenanceRecords as $record)
                                            <tr>
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
                                                <td>
                                                    <a href="{{ route('maintenance.show', $record) }}" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No maintenance records found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Movement Modal -->
<div class="modal fade" id="movementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('assets.movement', $asset) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Log Asset Movement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Movement Date <span class="text-danger">*</span></label>
                                <input type="date" name="movement_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Movement Type <span class="text-danger">*</span></label>
                                <select name="movement_type" class="form-control" required>
                                    <option value="assignment">Assignment</option>
                                    <option value="location_change">Location Change</option>
                                    <option value="return">Return</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From (Employee)</label>
                                <select name="from_user_id" class="form-control">
                                    <option value="">None</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From (Location)</label>
                                <input type="text" name="from_location" class="form-control" placeholder="e.g., Room 101">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To (Employee)</label>
                                <select name="to_user_id" class="form-control">
                                    <option value="">None</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To (Location)</label>
                                <input type="text" name="to_location" class="form-control" placeholder="e.g., Room 102">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" name="reason" class="form-control" placeholder="Reason for movement">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Movement</button>
                </div>
            </form>
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



