@extends('layouts.contentLayoutMaster')

@section('title', 'Edit Maintenance Record')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Maintenance Record</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('maintenance.update', $record) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Asset <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-control @error('asset_id') is-invalid @enderror" required>
                                    <option value="">Select Asset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id', $record->asset_id) == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->name }} ({{ $asset->asset_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="scheduled" {{ old('type', $record->type) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="repair" {{ old('type', $record->type) == 'repair' ? 'selected' : '' }}>Repair</option>
                                    <option value="inspection" {{ old('type', $record->type) == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                    <option value="other" {{ old('type', $record->type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Scheduled Date <span class="text-danger">*</span></label>
                                <input type="date" name="scheduled_date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                                       value="{{ old('scheduled_date', $record->scheduled_date->format('Y-m-d')) }}" required>
                                @error('scheduled_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Completed Date</label>
                                <input type="date" name="completed_date" class="form-control @error('completed_date') is-invalid @enderror" 
                                       value="{{ old('completed_date', $record->completed_date ? $record->completed_date->format('Y-m-d') : '') }}">
                                @error('completed_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $record->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status', $record->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $record->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $record->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $record->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Work Performed</label>
                        <textarea name="work_performed" class="form-control @error('work_performed') is-invalid @enderror" rows="3">{{ old('work_performed', $record->work_performed) }}</textarea>
                        @error('work_performed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cost</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="cost" step="0.01" min="0" 
                                           class="form-control @error('cost') is-invalid @enderror" 
                                           value="{{ old('cost', $record->cost) }}">
                                </div>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Service Provider</label>
                                <input type="text" name="service_provider" class="form-control @error('service_provider') is-invalid @enderror" 
                                       value="{{ old('service_provider', $record->service_provider) }}">
                                @error('service_provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Next Maintenance Date</label>
                        <input type="date" name="next_maintenance_date" class="form-control @error('next_maintenance_date') is-invalid @enderror" 
                               value="{{ old('next_maintenance_date', $record->next_maintenance_date ? $record->next_maintenance_date->format('Y-m-d') : '') }}">
                        @error('next_maintenance_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $record->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Record</button>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
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



