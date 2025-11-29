@extends('layouts.contentLayoutMaster')

@section('title', 'Edit Expense')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Expense</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <div class="mb-2">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="category_existing" name="category_type" value="existing" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="category_existing">Select Existing</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="category_new" name="category_type" value="new" class="custom-control-input">
                                        <label class="custom-control-label" for="category_new">Create New</label>
                                    </div>
                                </div>
                                
                                <select name="expense_category_id" id="expense_category_id" class="form-control @error('expense_category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <input type="text" name="category_name" id="category_name" class="form-control @error('category_name') is-invalid @enderror" 
                                       placeholder="Enter new category name" style="display: none;" value="{{ old('category_name') }}">
                                
                                @error('expense_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('category_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">If category doesn't exist, select "Create New" and enter the category name.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Expense Date <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="amount" step="0.01" min="0" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount', $expense->amount) }}" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vendor/Payee</label>
                                <input type="text" name="vendor_payee" class="form-control @error('vendor_payee') is-invalid @enderror" 
                                       value="{{ old('vendor_payee', $expense->vendor_payee) }}" placeholder="Enter vendor or payee name">
                                @error('vendor_payee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" 
                                  placeholder="Enter expense description">{{ old('description', $expense->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Receipt/Invoice</label>
                        @if($expense->receipt_path)
                            <div class="mb-1">
                                <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="file" class="mr-1"></i> View Current Receipt
                                </a>
                            </div>
                        @endif
                        <input type="file" name="receipt" class="form-control-file @error('receipt') is-invalid @enderror" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 5MB). Leave empty to keep current file.</small>
                        @error('receipt')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Expense</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
    
    // Handle category type toggle
    $('input[name="category_type"]').on('change', function() {
        if ($(this).val() === 'new') {
            $('#expense_category_id').hide().prop('required', false);
            $('#category_name').show().prop('required', true).focus();
        } else {
            $('#category_name').hide().prop('required', false).val('');
            $('#expense_category_id').show().prop('required', true);
        }
    });
    
    // Set initial state based on old input
    @if(old('category_type') === 'new' || old('category_name'))
        $('#category_new').prop('checked', true).trigger('change');
    @endif
});
</script>
@endsection

