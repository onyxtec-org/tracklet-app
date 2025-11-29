<?php $__env->startSection('title', 'Expense Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Expense Details</h4>
                <div>
                    <a href="<?php echo e(route('expenses.edit', $expense)); ?>" class="btn btn-primary">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-outline-secondary">
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
                                <td><?php echo e($expense->expense_date->format('F d, Y')); ?></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><span class="badge badge-light-primary"><?php echo e($expense->category->name); ?></span></td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td><strong class="text-primary">$<?php echo e(number_format($expense->amount, 2)); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Vendor/Payee:</th>
                                <td><?php echo e($expense->vendor_payee ?? '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Created By:</th>
                                <td><?php echo e($expense->user->name); ?></td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td><?php echo e($expense->created_at->format('F d, Y h:i A')); ?></td>
                            </tr>
                            <tr>
                                <th>Receipt:</th>
                                <td>
                                    <?php if($expense->receipt_path): ?>
                                        <a href="<?php echo e(asset('storage/' . $expense->receipt_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i data-feather="file" class="mr-1"></i> View Receipt
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No receipt attached</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if($expense->description): ?>
                <div class="row">
                    <div class="col-12">
                        <h6>Description:</h6>
                        <p><?php echo e($expense->description); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
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


<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/expenses/show.blade.php ENDPATH**/ ?>