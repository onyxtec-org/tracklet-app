@extends('layouts.contentLayoutMaster')

@section('title', 'Edit Inventory Item')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Inventory Item</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.items.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $item->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" 
                                       value="{{ old('category', $item->category) }}" placeholder="e.g., Office Supplies, Electronics">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', $item->quantity) }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Minimum Threshold <span class="text-danger">*</span></label>
                                <input type="number" name="minimum_threshold" min="0" class="form-control @error('minimum_threshold') is-invalid @enderror" 
                                       value="{{ old('minimum_threshold', $item->minimum_threshold) }}" required>
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
                                       value="{{ old('unit', $item->unit) }}" placeholder="pcs, kg, liters, etc.">
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
                                           value="{{ old('unit_price', $item->unit_price) }}" required>
                                </div>
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Item</button>
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



