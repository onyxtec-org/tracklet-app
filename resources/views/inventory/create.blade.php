@extends('layouts.contentLayoutMaster')

@section('title', 'Add Inventory Item')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add New Inventory Item</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.items.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" 
                                       value="{{ old('category') }}" placeholder="e.g., Office Supplies, Electronics">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Initial Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', 0) }}" required>
                                <small class="text-muted">Starting stock quantity. Use Stock In/Out to track changes after creation.</small>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Minimum Threshold <span class="text-danger">*</span></label>
                                <input type="number" name="minimum_threshold" min="0" class="form-control @error('minimum_threshold') is-invalid @enderror" 
                                       value="{{ old('minimum_threshold', 0) }}" required>
                                <small class="text-muted">Alert when stock falls below this</small>
                                @error('minimum_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit', 'pcs') }}" placeholder="pcs, kg, liters, etc.">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Unit Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="unit_price" step="0.01" min="0" 
                                           class="form-control @error('unit_price') is-invalid @enderror" 
                                           value="{{ old('unit_price', 0) }}" required>
                                </div>
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-left-3 border-left-info mb-3 shadow-sm">
                        <div class="alert-body">
                            <div class="d-flex align-items-start">
                                <i data-feather="info" class="font-medium-3 mr-2 mt-25"></i>
                                <div>
                                    <h6 class="alert-heading mb-1 font-weight-bolder">How Stock Tracking Works</h6>
                                    <p class="mb-0 small">
                                        <strong>Initial Quantity:</strong> Set the starting stock when creating the item.<br>
                                        <strong>After Creation:</strong> Use <strong>Stock In</strong> to add more inventory (purchases) or <strong>Stock Out</strong> to remove inventory (usage/consumption). 
                                        All transactions are tracked automatically and the quantity updates in real-time.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save Item</button>
                        <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary">Cancel</a>
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

