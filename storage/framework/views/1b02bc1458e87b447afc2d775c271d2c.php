<?php $__env->startSection('title', 'Forgot Password'); ?>

<?php $__env->startSection('page-style'); ?>

<link rel="stylesheet" href="<?php echo e(asset(mix('css/base/pages/page-auth.css'))); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-wrapper auth-v1 px-2">
  
  <div class="auth-inner py-2">
    <?php if(session('success')): ?>
    <div class="alert alert-success border-left-3 border-left-success alert-dismissible fade show shadow-sm mb-2" role="alert">
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
    <div class="alert alert-danger border-left-3 border-left-danger alert-dismissible fade show shadow-sm mb-2" role="alert">
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
    <?php endif; ?>
  
    <?php if($errors->any()): ?>
    <div class="alert alert-danger border-left-3 border-left-danger alert-dismissible fade show alert-validation-msg shadow-sm mb-2" role="alert">
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
    <!-- Forgot Password v1 -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="javascript:void(0);" class="brand-logo" style="display: flex; justify-content: center; align-items: center; margin-bottom: 1.5rem; padding: 0.5rem 0;">
          <img src="<?php echo e(asset('images/logo/LOGO.svg')); ?>" alt="TrackLet" style="max-width: 200px; max-height: 100px; height: auto; width: auto; object-fit: contain; display: block;">
        </a>

        <h4 class="card-title mb-1">Forgot Password? 🔒</h4>
        <p class="card-text mb-2">Enter your email and we'll send you instructions to reset your password</p>

        <form class="auth-forgot-password-form mt-2" method="POST" action="<?php echo e(route('password.email')); ?>">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label for="forgot-password-email" class="form-label">Email</label>
            <input type="text" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="forgot-password-email" name="email" value="<?php echo e(old('email')); ?>" placeholder="john@example.com" aria-describedby="forgot-password-email" tabindex="1" autofocus />
             <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <span class="invalid-feedback" role="alert">
                <strong><?php echo e($message); ?></strong>
              </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <button type="submit" class="btn btn-primary btn-block" tabindex="2">Send reset link</button>
        </form>

        <p class="text-center mt-2">
          <?php if(Route::has('login')): ?>
          <a href="<?php echo e(route('login')); ?>"> <i data-feather="chevron-left"></i> Back to login </a>
          <?php endif; ?>
        </p>
      </div>
    </div>
    <!-- /Forgot Password v1 -->
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/fullLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views//auth/passwords/email.blade.php ENDPATH**/ ?>