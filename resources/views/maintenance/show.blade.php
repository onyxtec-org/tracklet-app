@extends('layouts.contentLayoutMaster')

@section('title', 'Maintenance Record Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Maintenance Record Details</h4>
                <div>
                    <a href="{{ route('maintenance.edit', $record) }}" class="btn btn-primary">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Asset:</th>
                                <td><strong>{{ $record->asset->name }}</strong><br><small class="text-muted">{{ $record->asset->asset_code }}</small></td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td><span class="badge badge-light-primary">{{ ucfirst($record->type) }}</span></td>
                            </tr>
                            <tr>
                                <th>Scheduled Date:</th>
                                <td>{{ $record->scheduled_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
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
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Completed Date:</th>
                                <td>{{ $record->completed_date ? $record->completed_date->format('F d, Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Cost:</th>
                                <td>{{ $record->cost ? '$' . number_format($record->cost, 2) : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Service Provider:</th>
                                <td>{{ $record->service_provider ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Next Maintenance:</th>
                                <td>{{ $record->next_maintenance_date ? $record->next_maintenance_date->format('F d, Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <h6>Description:</h6>
                        <p>{{ $record->description }}</p>
                    </div>
                </div>

                @if($record->work_performed)
                <div class="row mb-2">
                    <div class="col-12">
                        <h6>Work Performed:</h6>
                        <p>{{ $record->work_performed }}</p>
                    </div>
                </div>
                @endif

                @if($record->notes)
                <div class="row mb-2">
                    <div class="col-12">
                        <h6>Notes:</h6>
                        <p>{{ $record->notes }}</p>
                    </div>
                </div>
                @endif
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



