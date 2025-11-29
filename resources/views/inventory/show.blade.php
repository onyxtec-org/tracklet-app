@extends('layouts.contentLayoutMaster')

@section('title', 'Inventory Item Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $item->name }}</h4>
                <div>
                    <a href="{{ route('inventory.items.edit', $item) }}" class="btn btn-primary">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $item->name }}</td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td>{{ $item->category ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Quantity:</th>
                                <td><strong>{{ $item->quantity }} {{ $item->unit }}</strong></td>
                            </tr>
                            <tr>
                                <th>Minimum Threshold:</th>
                                <td>{{ $item->minimum_threshold }} {{ $item->unit }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Unit Price:</th>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Total Value:</th>
                                <td><strong class="text-primary">${{ number_format($item->total_price, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($item->isLowStock())
                                        <span class="badge badge-light-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-light-success">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>Stock Transactions</h5>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success mr-1" data-toggle="modal" data-target="#stockModal" onclick="setStockType('in')">
                                        <i data-feather="arrow-down" class="mr-1"></i> Stock In
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#stockModal" onclick="setStockType('out')">
                                        <i data-feather="arrow-up" class="mr-1"></i> Stock Out
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @php
                                    $stockInTotal = $item->stockTransactions->where('type', 'in')->sum('quantity');
                                    $stockOutTotal = $item->stockTransactions->where('type', 'out')->sum('quantity');
                                @endphp
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card border-left-success">
                                            <div class="card-body p-2">
                                                <h6 class="mb-0 text-success">Total Stock In</h6>
                                                <h4 class="mb-0">{{ number_format($stockInTotal) }} {{ $item->unit }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-left-danger">
                                            <div class="card-body p-2">
                                                <h6 class="mb-0 text-danger">Total Stock Out</h6>
                                                <h4 class="mb-0">{{ number_format($stockOutTotal) }} {{ $item->unit }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-left-primary">
                                            <div class="card-body p-2">
                                                <h6 class="mb-0 text-primary">Current Stock</h6>
                                                <h4 class="mb-0">{{ number_format($item->quantity) }} {{ $item->unit }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>Reference</th>
                                                <th>Notes</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($item->stockTransactions->sortByDesc('transaction_date') as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                                <td>
                                                    @if($transaction->type == 'in')
                                                        <span class="badge badge-light-success">
                                                            <i data-feather="arrow-down" class="font-small-2 mr-50"></i> Stock In
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light-danger">
                                                            <i data-feather="arrow-up" class="font-small-2 mr-50"></i> Stock Out
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($transaction->type == 'in')
                                                        <span class="text-success">+{{ $transaction->quantity }}</span>
                                                    @else
                                                        <span class="text-danger">-{{ $transaction->quantity }}</span>
                                                    @endif
                                                    {{ $item->unit }}
                                                </td>
                                                <td>{{ $transaction->reference ?? '-' }}</td>
                                                <td>{{ $transaction->notes ?? '-' }}</td>
                                                <td>{{ $transaction->user->name }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No transactions found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Transaction Modal -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('inventory.items.stock', $item) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Log Stock Transaction</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" id="stockTransactionType" value="in" required>
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <div id="transactionTypeDisplay" class="alert alert-info mb-2">
                            <i data-feather="arrow-down" class="mr-1"></i> <strong>Stock In</strong> - Adding inventory
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" min="1" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Purchase order, usage reason, etc.">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <div id="stockInFields">
                        <div class="form-group">
                            <label>Unit Price</label>
                            <input type="number" name="unit_price" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Vendor</label>
                            <input type="text" name="vendor" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
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

    function setStockType(type) {
        $('#stockTransactionType').val(type);
        updateTransactionTypeDisplay(type);
    }

    function updateTransactionTypeDisplay(type) {
        const displayDiv = $('#transactionTypeDisplay');
        if (type == 'in') {
            displayDiv.removeClass('alert-danger').addClass('alert-success');
            displayDiv.html('<i data-feather="arrow-down" class="mr-1"></i> <strong>Stock In</strong> - Adding inventory to stock');
            $('#stockInFields').show();
            $('.modal-title').html('<i data-feather="arrow-down" class="mr-1 text-success"></i> Log Stock In');
        } else {
            displayDiv.removeClass('alert-success').addClass('alert-danger');
            displayDiv.html('<i data-feather="arrow-up" class="mr-1"></i> <strong>Stock Out</strong> - Removing inventory from stock');
            $('#stockInFields').hide();
            $('.modal-title').html('<i data-feather="arrow-up" class="mr-1 text-danger"></i> Log Stock Out');
        }
        if (feather) {
            feather.replace();
        }
    }

    // Set default to 'in' when modal opens without button click
    $('#stockModal').on('show.bs.modal', function(e) {
        const type = $('#stockTransactionType').val() || 'in';
        updateTransactionTypeDisplay(type);
    });
});
</script>
@endsection

