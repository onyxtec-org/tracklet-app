<?php $__env->startSection('title', 'Change Password'); ?>

<?php $__env->startSection('page-style'); ?>

<link rel="stylesheet" href="<?php echo e(asset(mix('css/base/pages/page-auth.css'))); ?>">
<style>
  .brand-logo {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 0.5rem 0;
  }
  .brand-logo img {
    max-width: 150px;
    max-height: 80px;
    height: auto;
    width: auto;
    object-fit: contain;
    display: block;
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-wrapper auth-v1 px-2">
  <div class="auth-inner py-2">
    <!-- Change Password -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="javascript:void(0);" class="brand-logo">
          <img src="<?php echo e(asset('images/logo/LOGO.svg')); ?>" alt="TrackLet">
        </a>

        <h4 class="card-title mb-1">Change Your Password 🔒</h4>
        <p class="card-text mb-2">For security reasons, you must change your password before continuing.</p>

        <?php if(session('warning')): ?>
        <div class="alert alert-warning border-left-3 border-left-warning shadow-sm" role="alert">
          <div class="alert-body">
            <div class="d-flex align-items-start">
              <i data-feather="alert-triangle" class="font-medium-3 mr-2 mt-25"></i>
              <div>
                <h6 class="alert-heading mb-1 font-weight-bolder">Warning</h6>
                <p class="mb-0"><?php echo e(session('warning')); ?></p>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div class="alert alert-danger border-left-3 border-left-danger shadow-sm" role="alert">
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
          </div>
        </div>
        <?php endif; ?>

        <form class="auth-change-password-form mt-2" method="POST" action="<?php echo e(route('password.change.submit')); ?>">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label for="current-password" class="form-label">Current Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                     id="current-password" name="current_password" tabindex="1" 
                     placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                     aria-describedby="current-password" required autofocus />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
              <?php $__errorArgs = ['current_password'];
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
          </div>

          <div class="form-group">
            <label for="new-password" class="form-label">New Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                     id="new-password" name="new_password" tabindex="2" 
                     placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                     aria-describedby="new-password" required minlength="8" />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
            <?php $__errorArgs = ['new_password'];
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

          <div class="form-group">
            <label for="new-password-confirmation" class="form-label">Confirm New Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge" 
                     id="new-password-confirmation" name="new_password_confirmation" tabindex="3" 
                     placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                     aria-describedby="new-password-confirmation" required minlength="8" />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-block" tabindex="4">Change Password</button>
        </form>
      </div>
    </div>
    <!-- /Change Password -->
  </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/fullLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/auth/change-password.blade.php ENDPATH**/ ?>