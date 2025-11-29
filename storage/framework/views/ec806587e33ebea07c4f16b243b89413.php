<?php if($errors->any()): ?>
<div class="alert alert-danger border-left-3 border-left-danger alert-dismissible fade show alert-validation-msg shadow-sm" role="alert">
    <div class="alert-body">
        <div class="d-flex align-items-start">
            <i data-feather="alert-circle" class="font-medium-3 mr-2 mt-25"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1 font-weight-bolder">Validation Errors</h6>
                <ul class="mb-0 pl-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<?php endif; ?>

<?php if(session('success')): ?>
<div class="alert alert-success border-left-3 border-left-success alert-dismissible fade show shadow-sm" role="alert">
    <div class="alert-body">
        <div class="d-flex align-items-center">
            <i data-feather="check-circle" class="font-medium-3 mr-2"></i>
            <div>
                <strong class="d-block mb-50">Success!</strong>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="alert alert-danger border-left-3 border-left-danger alert-dismissible fade show shadow-sm" role="alert">
    <div class="alert-body">
        <div class="d-flex align-items-center">
            <i data-feather="x-circle" class="font-medium-3 mr-2"></i>
            <div>
                <strong class="d-block mb-50">Error!</strong>
                <span><?php echo e(session('error')); ?></span>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<?php endif; ?> <?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/panels/response.blade.php ENDPATH**/ ?>