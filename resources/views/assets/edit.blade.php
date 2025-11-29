@extends('layouts.contentLayoutMaster')

@section('title', 'Edit Asset')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Asset - {{ $asset->asset_code }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('assets.update', $asset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Asset Code</label>
                                <input type="text" class="form-control" value="{{ $asset->asset_code }}" disabled>
                                <small class="text-muted">Asset code cannot be changed</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $asset->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" 
                                       value="{{ old('category', $asset->category) }}" required>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="in_repair" {{ old('status', $asset->status) == 'in_repair' ? 'selected' : '' }}>In Repair</option>
                                    <option value="retired" {{ old('status', $asset->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div id="statusReason" style="{{ old('status', $asset->status) == 'retired' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Status Change Reason <span class="text-danger">*</span></label>
                            <textarea name="status_change_reason" class="form-control @error('status_change_reason') is-invalid @enderror" rows="2">{{ old('status_change_reason', $asset->status_change_reason) }}</textarea>
                            <small class="text-muted">Required when status is set to Retired</small>
                            @error('status_change_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                       value="{{ old('purchase_date', $asset->purchase_date->format('Y-m-d')) }}" required>
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="purchase_price" step="0.01" min="0" 
                                           class="form-control @error('purchase_price') is-invalid @enderror" 
                                           value="{{ old('purchase_price', $asset->purchase_price) }}" required>
                                </div>
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vendor</label>
                                <input type="text" name="vendor" class="form-control @error('vendor') is-invalid @enderror" 
                                       value="{{ old('vendor', $asset->vendor) }}">
                                @error('vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Warranty Expiry</label>
                                <input type="date" name="warranty_expiry" class="form-control @error('warranty_expiry') is-invalid @enderror" 
                                       value="{{ old('warranty_expiry', $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : '') }}">
                                @error('warranty_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned To (Employee)</label>
                                <select name="assigned_to_user_id" class="form-control @error('assigned_to_user_id') is-invalid @enderror">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to_user_id', $asset->assigned_to_user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to_user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned To (Location)</label>
                                <input type="text" name="assigned_to_location" class="form-control @error('assigned_to_location') is-invalid @enderror" 
                                       value="{{ old('assigned_to_location', $asset->assigned_to_location) }}" placeholder="e.g., Room 101, Marketing Dept">
                                @error('assigned_to_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $asset->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $asset->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Asset</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">Cancel</a>
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

    $('select[name="status"]').on('change', function() {
        if ($(this).val() == 'retired') {
            $('#statusReason').show();
            $('#statusReason textarea').prop('required', true);
        } else {
            $('#statusReason').hide();
            $('#statusReason textarea').prop('required', false);
        }
    });
});
</script>
@endsection



