<?php $__env->startSection('title', 'Upcoming Maintenance'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Upcoming Maintenance (Next 7 Days)</h4>
                <a href="<?php echo e(route('maintenance.index')); ?>" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-1"></i> Back to All Records
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-left-3 border-left-info shadow-sm">
                    <div class="alert-body">
                        <div class="d-flex align-items-start">
                            <i data-feather="calendar" class="font-medium-3 mr-2 mt-25"></i>
                            <div>
                                <h6 class="alert-heading mb-1 font-weight-bolder">Upcoming Maintenance</h6>
                                <p class="mb-0">Maintenance scheduled within the next 7 days.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Scheduled Date</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($record->asset->name); ?></strong><br><small class="text-muted"><?php echo e($record->asset->asset_code); ?></small></td>
                                <td><span class="badge badge-light-primary"><?php echo e(ucfirst($record->type)); ?></span></td>
                                <td>
                                    <strong><?php echo e($record->scheduled_date->format('M d, Y')); ?></strong><br>
                                    <small class="text-muted"><?php echo e($record->scheduled_date->diffForHumans()); ?></small>
                                </td>
                                <td><?php echo e(Str::limit($record->description, 60)); ?></td>
                                <td>
                                    <a href="<?php echo e(route('maintenance.show', $record)); ?>" class="btn btn-sm btn-primary">
                                        <i data-feather="eye" class="mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">No upcoming maintenance scheduled.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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


<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/maintenance/upcoming.blade.php ENDPATH**/ ?>