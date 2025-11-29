<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css'))); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Users</h4>
                <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                    <i data-feather="plus" class="mr-1"></i> Add User
                </a>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="<?php echo e(route('users.index')); ?>" class="mb-2">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="">All Roles</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->name); ?>" <?php echo e(request('role') == $role->name ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Search by name or email...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Organization</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($user->name); ?></strong></td>
                                <td><?php echo e($user->email); ?></td>
                                <td>
                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge badge-light-primary"><?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td><?php echo e($user->organization->name ?? '-'); ?></td>
                                <td><?php echo e($user->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <?php if(!$user->isSuperAdmin() && $user->id !== auth()->id()): ?>
                                        <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
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
                                <td colspan="6" class="text-center">No users found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <?php echo e($users->links()); ?>

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




<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/users/index.blade.php ENDPATH**/ ?>