<?php $__env->startSection('title', 'Expense Reports'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/charts/apexcharts.css'))); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Expense Reports</h4>
                <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-1"></i> Back to Expenses
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('expenses.reports')); ?>" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Period</label>
                            <select name="period" class="form-control">
                                <option value="monthly" <?php echo e(request('period', 'monthly') == 'monthly' ? 'selected' : ''); ?>>Monthly</option>
                                <option value="quarterly" <?php echo e(request('period') == 'quarterly' ? 'selected' : ''); ?>>Quarterly</option>
                                <option value="ytd" <?php echo e(request('period') == 'ytd' ? 'selected' : ''); ?>>Year to Date</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Year</label>
                            <select name="year" class="form-control">
                                <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                    <option value="<?php echo e($y); ?>" <?php echo e(request('year', date('Y')) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3" id="month-select" style="<?php echo e(request('period', 'monthly') != 'monthly' ? 'display:none;' : ''); ?>">
                            <label>Month</label>
                            <select name="month" class="form-control">
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo e($m); ?>" <?php echo e(request('month', date('m')) == $m ? 'selected' : ''); ?>>
                                        <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3" id="quarter-select" style="<?php echo e(request('period') != 'quarterly' ? 'display:none;' : ''); ?>">
                            <label>Quarter</label>
                            <select name="quarter" class="form-control">
                                <?php for($q = 1; $q <= 4; $q++): ?>
                                    <option value="<?php echo e($q); ?>" <?php echo e(request('quarter', ceil(date('m')/3)) == $q ? 'selected' : ''); ?>>
                                        Q<?php echo e($q); ?>

                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                    </div>
                </form>

                <?php if(isset($total_amount)): ?>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="card bg-light-primary">
                            <div class="card-body">
                                <h6 class="mb-0">Total Amount</h6>
                                <h3 class="mb-0">$<?php echo e(number_format($total_amount, 2)); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light-info">
                            <div class="card-body">
                                <h6 class="mb-0">Total Expenses</h6>
                                <h3 class="mb-0"><?php echo e($total_count); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light-success">
                            <div class="card-body">
                                <h6 class="mb-0">Average Amount</h6>
                                <h3 class="mb-0">$<?php echo e(number_format($total_count > 0 ? $total_amount / $total_count : 0, 2)); ?></h3>
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
                                            <?php $__currentLoopData = $category_totals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($cat['category']); ?></td>
                                                <td><?php echo e($cat['count']); ?></td>
                                                <td><strong>$<?php echo e(number_format($cat['total'], 2)); ?></strong></td>
                                                <td><?php echo e($total_amount > 0 ? number_format(($cat['total'] / $total_amount) * 100, 2) : 0); ?>%</td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset(mix('vendors/js/charts/apexcharts.min.js'))); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
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
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/expenses/reports.blade.php ENDPATH**/ ?>