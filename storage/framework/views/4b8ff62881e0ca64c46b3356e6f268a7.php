<?php $__env->startSection('title', 'Maintenance Records'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Maintenance Records</h4>
                <div>
                    <?php if($upcoming_count > 0): ?>
                        <a href="<?php echo e(route('maintenance.upcoming')); ?>" class="btn btn-warning mr-1">
                            <i data-feather="calendar" class="mr-1"></i> Upcoming (<?php echo e($upcoming_count); ?>)
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('maintenance.create')); ?>" class="btn btn-primary">
                        <i data-feather="plus" class="mr-1"></i> Add Record
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="<?php echo e(route('maintenance.index')); ?>" class="mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                                <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
                                <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="scheduled" <?php echo e(request('type') == 'scheduled' ? 'selected' : ''); ?>>Scheduled</option>
                                <option value="repair" <?php echo e(request('type') == 'repair' ? 'selected' : ''); ?>>Repair</option>
                                <option value="inspection" <?php echo e(request('type') == 'inspection' ? 'selected' : ''); ?>>Inspection</option>
                                <option value="other" <?php echo e(request('type') == 'other' ? 'selected' : ''); ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Asset</label>
                            <select name="asset_id" class="form-control">
                                <option value="">All Assets</option>
                                <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($asset->id); ?>" <?php echo e(request('asset_id') == $asset->id ? 'selected' : ''); ?>><?php echo e($asset->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="<?php echo e(route('maintenance.index')); ?>" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Scheduled Date</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <?php if($record->asset): ?>
                                        <strong><?php echo e($record->asset->name); ?></strong><br><small class="text-muted"><?php echo e($record->asset->asset_code); ?></small>
                                    <?php else: ?>
                                        <strong class="text-muted">N/A (Asset Deleted)</strong>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-light-primary"><?php echo e(ucfirst($record->type)); ?></span></td>
                                <td><?php echo e($record->scheduled_date->format('M d, Y')); ?></td>
                                <td>
                                    <?php if($record->status == 'pending'): ?>
                                        <span class="badge badge-light-warning">Pending</span>
                                    <?php elseif($record->status == 'in_progress'): ?>
                                        <span class="badge badge-light-info">In Progress</span>
                                    <?php elseif($record->status == 'completed'): ?>
                                        <span class="badge badge-light-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge badge-light-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(Str::limit($record->description, 50)); ?></td>
                                <td><?php echo e($record->cost ? '$' . number_format($record->cost, 2) : '-'); ?></td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="<?php echo e(route('maintenance.show', $record)); ?>" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('maintenance.edit', $record)); ?>" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('maintenance.destroy', $record)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">No maintenance records found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <?php echo e($records->links()); ?>

                </div>
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




<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/maintenance/index.blade.php ENDPATH**/ ?>