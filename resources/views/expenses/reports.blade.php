@extends('layouts.contentLayoutMaster')

@section('title', 'Expense Reports')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Expense Reports</h4>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-1"></i> Back to Expenses
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('expenses.reports') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Period</label>
                            <select name="period" class="form-control">
                                <option value="monthly" {{ request('period', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="ytd" {{ request('period') == 'ytd' ? 'selected' : '' }}>Year to Date</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Year</label>
                            <select name="year" class="form-control">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3" id="month-select" style="{{ request('period', 'monthly') != 'monthly' ? 'display:none;' : '' }}">
                            <label>Month</label>
                            <select name="month" class="form-control">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month', date('m')) == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3" id="quarter-select" style="{{ request('period') != 'quarterly' ? 'display:none;' : '' }}">
                            <label>Quarter</label>
                            <select name="quarter" class="form-control">
                                @for($q = 1; $q <= 4; $q++)
                                    <option value="{{ $q }}" {{ request('quarter', ceil(date('m')/3)) == $q ? 'selected' : '' }}>
                                        Q{{ $q }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                    </div>
                </form>

                @if(isset($total_amount))
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="card bg-light-primary">
                            <div class="card-body">
                                <h6 class="mb-0">Total Amount</h6>
                                <h3 class="mb-0">${{ number_format($total_amount, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light-info">
                            <div class="card-body">
                                <h6 class="mb-0">Total Expenses</h6>
                                <h3 class="mb-0">{{ $total_count }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light-success">
                            <div class="card-body">
                                <h6 class="mb-0">Average Amount</h6>
                                <h3 class="mb-0">${{ number_format($total_count > 0 ? $total_amount / $total_count : 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Category Breakdown</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Count</th>
                                                <th>Total Amount</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($category_totals as $cat)
                                            <tr>
                                                <td>{{ $cat['category'] }}</td>
                                                <td>{{ $cat['count'] }}</td>
                                                <td><strong>${{ number_format($cat['total'], 2) }}</strong></td>
                                                <td>{{ $total_amount > 0 ? number_format(($cat['total'] / $total_amount) * 100, 2) : 0 }}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection

@section('page-script')
<script>
$(function() {
    if (feather) {
        feather.replace({ width: 14, height: 14 });
    }

    $('select[name="period"]').on('change', function() {
        if ($(this).val() == 'monthly') {
            $('#month-select').show();
            $('#quarter-select').hide();
        } else if ($(this).val() == 'quarterly') {
            $('#month-select').hide();
            $('#quarter-select').show();
        } else {
            $('#month-select').hide();
            $('#quarter-select').hide();
        }
    });
});
</script>
@endsection



