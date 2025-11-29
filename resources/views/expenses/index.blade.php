@extends('layouts.contentLayoutMaster')

@section('title', 'Expenses')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
@endsection

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Expenses</h4>
                @if(!auth()->user()->hasRole('general_staff'))
                <div>
                    <a href="{{ route('expenses.reports') }}" class="btn btn-outline-primary mr-1">
                        <i data-feather="file-text" class="mr-1"></i> Reports
                    </a>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="mr-1"></i> Add Expense
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ route(auth()->user()->hasRole('general_staff') ? 'view.expenses' : 'expenses.index') }}" class="mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label>Vendor/Payee</label>
                            <input type="text" name="vendor" class="form-control" value="{{ request('vendor') }}" placeholder="Search vendor...">
                        </div>
                        @if(auth()->user()->hasRole('admin'))
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="approval_status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        @endif
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="{{ route(auth()->user()->hasRole('general_staff') ? 'view.expenses' : 'expenses.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table expenses-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Vendor/Payee</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td><span class="badge badge-light-primary">{{ $expense->category->name }}</span></td>
                                <td><strong>${{ number_format($expense->amount, 2) }}</strong></td>
                                <td>{{ $expense->vendor_payee ?? '-' }}</td>
                                <td>{{ Str::limit($expense->description, 50) }}</td>
                                <td>
                                    @if($expense->approval_status == 'approved')
                                        <span class="badge badge-light-success">Approved</span>
                                    @elseif($expense->approval_status == 'rejected')
                                        <span class="badge badge-light-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-light-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $expense->user->name }}</td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="{{ route(auth()->user()->hasRole('general_staff') ? 'view.expenses.show' : 'expenses.show', $expense) }}" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @if(!auth()->user()->hasRole('general_staff'))
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No expenses found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
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



