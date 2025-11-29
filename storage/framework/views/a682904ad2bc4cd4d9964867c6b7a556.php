<?php $__env->startSection('title', 'Expenses'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css'))); ?>">
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css'))); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Expenses</h4>
                <?php if(!auth()->user()->hasRole('general_staff')): ?>
                <div>
                    <a href="<?php echo e(route('expenses.reports')); ?>" class="btn btn-outline-primary mr-1">
                        <i data-feather="file-text" class="mr-1"></i> Reports
                    </a>
                    <a href="<?php echo e(route('expenses.create')); ?>" class="btn btn-primary">
                        <i data-feather="plus" class="mr-1"></i> Add Expense
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.expenses' : 'expenses.index')); ?>" class="mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">All Categories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Vendor/Payee</label>
                            <input type="text" name="vendor" class="form-control" value="<?php echo e(request('vendor')); ?>" placeholder="Search vendor...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.expenses' : 'expenses.index')); ?>" class="btn btn-outline-secondary">Clear</a>
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
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($expense->expense_date->format('M d, Y')); ?></td>
                                <td><span class="badge badge-light-primary"><?php echo e($expense->category->name); ?></span></td>
                                <td><strong>$<?php echo e(number_format($expense->amount, 2)); ?></strong></td>
                                <td><?php echo e($expense->vendor_payee ?? '-'); ?></td>
                                <td><?php echo e(Str::limit($expense->description, 50)); ?></td>
                                <td><?php echo e($expense->user->name); ?></td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.expenses.show' : 'expenses.show', $expense)); ?>" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <?php if(!auth()->user()->hasRole('general_staff')): ?>
                                        <a href="<?php echo e(route('expenses.edit', $expense)); ?>" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('expenses.destroy', $expense)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">No expenses found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <?php echo e($expenses->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js'))); ?>"></script>
<script src="<?php echo e(asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js'))); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
$(function() {
    if (feather) {
        feather.replace({ width: 14, height: 14 });
    }
});
</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/expenses/index.blade.php ENDPATH**/ ?>