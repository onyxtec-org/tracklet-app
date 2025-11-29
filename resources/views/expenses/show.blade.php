@extends('layouts.contentLayoutMaster')

@section('title', 'Expense Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Expense Details</h4>
                <div>
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Date:</th>
                                <td>{{ $expense->expense_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><span class="badge badge-light-primary">{{ $expense->category->name }}</span></td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td><strong class="text-primary">${{ number_format($expense->amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Vendor/Payee:</th>
                                <td>{{ $expense->vendor_payee ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Created By:</th>
                                <td>{{ $expense->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $expense->created_at->format('F d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Receipt:</th>
                                <td>
                                    @if($expense->receipt_path)
                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i data-feather="file" class="mr-1"></i> View Receipt
                                        </a>
                                    @else
                                        <span class="text-muted">No receipt attached</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($expense->description)
                <div class="row">
                    <div class="col-12">
                        <h6>Description:</h6>
                        <p>{{ $expense->description }}</p>
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

