<?php $__env->startSection('title', 'Asset Management'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css'))); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Assets</h4>
                <?php if(!auth()->user()->hasRole('general_staff')): ?>
                <a href="<?php echo e(route('assets.create')); ?>" class="btn btn-primary">
                    <i data-feather="plus" class="mr-1"></i> Add Asset
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="card bg-light-primary">
                            <div class="card-body text-center">
                                <h6 class="mb-0">Total Assets</h6>
                                <h3 class="mb-0"><?php echo e($summary['total']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-success">
                            <div class="card-body text-center">
                                <h6 class="mb-0">Active</h6>
                                <h3 class="mb-0"><?php echo e($summary['active']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-warning">
                            <div class="card-body text-center">
                                <h6 class="mb-0">In Repair</h6>
                                <h3 class="mb-0"><?php echo e($summary['in_repair']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-danger">
                            <div class="card-body text-center">
                                <h6 class="mb-0">Retired</h6>
                                <h3 class="mb-0"><?php echo e($summary['retired']); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <form method="GET" action="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.assets' : 'assets.index')); ?>" class="mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="in_repair" <?php echo e(request('status') == 'in_repair' ? 'selected' : ''); ?>>In Repair</option>
                                <option value="retired" <?php echo e(request('status') == 'retired' ? 'selected' : ''); ?>>Retired</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Category</label>
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Assigned To</label>
                            <select name="assigned_to_user_id" class="form-control">
                                <option value="">All Users</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(request('assigned_to_user_id') == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Search assets...">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.assets' : 'assets.index')); ?>" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asset Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Purchase Date</th>
                                <th>Purchase Price</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($asset->asset_code); ?></strong></td>
                                <td><?php echo e($asset->name); ?></td>
                                <td><?php echo e($asset->category); ?></td>
                                <td><?php echo e($asset->purchase_date->format('M d, Y')); ?></td>
                                <td>$<?php echo e(number_format($asset->purchase_price, 2)); ?></td>
                                <td>
                                    <?php if($asset->status == 'active'): ?>
                                        <span class="badge badge-light-success">Active</span>
                                    <?php elseif($asset->status == 'in_repair'): ?>
                                        <span class="badge badge-light-warning">In Repair</span>
                                    <?php else: ?>
                                        <span class="badge badge-light-danger">Retired</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($asset->assignedToUser): ?>
                                        <?php echo e($asset->assignedToUser->name); ?>

                                    <?php elseif($asset->assigned_to_location): ?>
                                        <?php echo e($asset->assigned_to_location); ?>

                                    <?php else: ?>
                                        <span class="text-muted">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.assets.show' : 'assets.show', $asset)); ?>" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <?php if(!auth()->user()->hasRole('general_staff')): ?>
                                        <a href="<?php echo e(route('assets.edit', $asset)); ?>" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('assets.destroy', $asset)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                <td colspan="8" class="text-center">No assets found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <?php echo e($assets->links()); ?>

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




<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/assets/index.blade.php ENDPATH**/ ?>