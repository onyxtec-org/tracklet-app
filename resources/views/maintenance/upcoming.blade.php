@extends('layouts.contentLayoutMaster')

@section('title', 'Upcoming Maintenance')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Upcoming Maintenance (Next 7 Days)</h4>
                <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-1"></i> Back to All Records
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-left-3 border-left-info shadow-sm">
                    <div class="alert-body">
                        <div class="d-flex align-items-start">
                            <i data-feather="calendar" class="font-medium-3 mr-2 mt-25"></i>
                            <div>
                                <h6 class="alert-heading mb-1 font-weight-bolder">Upcoming Maintenance</h6>
                                <p class="mb-0">Maintenance scheduled within the next 7 days.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Scheduled Date</th>
                                <th>Description</th>
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
                                <td>
                                    <strong>{{ $record->scheduled_date->format('M d, Y') }}</strong><br>
                                    <small class="text-muted">{{ $record->scheduled_date->diffForHumans() }}</small>
                                </td>
                                <td>{{ Str::limit($record->description, 60) }}</td>
                                <td>
                                    <a href="{{ route('maintenance.show', $record) }}" class="btn btn-sm btn-primary">
                                        <i data-feather="eye" class="mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No upcoming maintenance scheduled.</td>
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

