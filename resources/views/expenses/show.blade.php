@extends('layouts.contentLayoutMaster')

@section('title', 'Expense Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Expense Details</h4>
                <div>
                    @if(auth()->user()->hasRole('admin') && $expense->approval_status == 'pending')
                    <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="d-inline mr-1" onsubmit="return confirm('Approve this expense?')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i data-feather="check" class="mr-1"></i> Approve
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger mr-1" data-toggle="modal" data-target="#rejectModal">
                        <i data-feather="x" class="mr-1"></i> Reject
                    </button>
                    @endif
                    @if(!auth()->user()->hasRole('general_staff') && ($expense->approval_status == 'pending' || auth()->user()->id == $expense->user_id))
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary mr-1">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    @endif
                    <a href="{{ route(auth()->user()->hasRole('general_staff') ? 'view.expenses' : 'expenses.index') }}" class="btn btn-outline-secondary">
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
                                <th width="40%">Status:</th>
                                <td>
                                    @if($expense->approval_status == 'approved')
                                        <span class="badge badge-light-success">Approved</span>
                                        @if($expense->approver)
                                            <br><small class="text-muted">Approved by {{ $expense->approver->name }} on {{ $expense->approved_at->format('M d, Y') }}</small>
                                        @endif
                                    @elseif($expense->approval_status == 'rejected')
                                        <span class="badge badge-light-danger">Rejected</span>
                                        @if($expense->approver)
                                            <br><small class="text-muted">Rejected by {{ $expense->approver->name }} on {{ $expense->approved_at->format('M d, Y') }}</small>
                                        @endif
                                        @if($expense->rejection_reason)
                                            <br><small class="text-danger">{{ $expense->rejection_reason }}</small>
                                        @endif
                                    @else
                                        <span class="badge badge-light-warning">Pending Approval</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created By:</th>
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

<!-- Reject Modal -->
@if(auth()->user()->hasRole('admin') && $expense->approval_status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Expense</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Rejection Reason (Optional)</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
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

