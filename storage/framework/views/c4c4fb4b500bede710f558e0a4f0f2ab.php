<?php $__env->startSection('title', 'Login Page'); ?>

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
    max-width: 200px;
    max-height: 100px;
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
    <!-- Login v1 -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="javascript:void(0);" class="brand-logo">
          <img src="<?php echo e(asset('images/logo/LOGO.svg')); ?>" alt="TrackLet">
        </a>

        <h4 class="card-title mb-1">Welcome to TrackLet! 👋</h4>
        <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>

        <form class="auth-login-form mt-2" method="POST" action="<?php echo e(route('login')); ?>">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label for="login-email" class="form-label">Email</label>
            <input type="text" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="login-email" name="email" placeholder="john@example.com" aria-describedby="login-email" tabindex="1" autofocus value="<?php echo e(old('email')); ?>" />
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

          <div class="form-group">
            <label for="login-password">Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge" id="login-password" name="password" tabindex="2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="login-password" />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
            <?php if(Route::has('password.request')): ?>
            <div class="text-right mt-1">
              <a href="<?php echo e(route('password.request')); ?>">
                <small>Forgot Password?</small>
              </a>
            </div>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input class="custom-control-input" type="checkbox" id="remember-me" name="remember-me" tabindex="3" <?php echo e(old('remember-me') ? 'checked' : ''); ?> />
              <label class="custom-control-label" for="remember-me"> Remember Me </label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block" tabindex="4">Sign in</button>
        </form>

        <p class="text-center mt-2">
          <span>New organization?</span>
          <a href="<?php echo e(route('organization.register.show')); ?>">
            <span>Register your organization</span>
          </a>
        </p>
      </div>
    </div>
    <!-- /Login v1 -->
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/fullLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views//auth/login.blade.php ENDPATH**/ ?>