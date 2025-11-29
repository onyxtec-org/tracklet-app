<?php $__env->startSection('title', 'Register Organization'); ?>

<?php $__env->startSection('page-style'); ?>

<link rel="stylesheet" href="<?php echo e(asset(mix('css/base/pages/page-auth.css'))); ?>">
<style>
  .auth-wrapper.auth-v1 .auth-inner {
    max-width: 50%;
    margin-left: -148px;
  }
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
    <!-- Register v1 -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="javascript:void(0);" class="brand-logo">
          <img src="<?php echo e(asset('images/logo/LOGO.svg')); ?>" alt="TrackLet">
        </a>

        <h4 class="card-title mb-1">Register Your Organization! 🚀</h4>
        <p class="card-text mb-2">Create your organization account and start the adventure</p>

        <form class="auth-register-form mt-2" method="POST" action="<?php echo e(route('organization.register')); ?>">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label for="organization_name" class="form-label">Organization Name</label>
            <input type="text" class="form-control <?php $__errorArgs = ['organization_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="organization_name" name="organization_name" placeholder="Acme Corporation" aria-describedby="organization_name" tabindex="1" autofocus value="<?php echo e(old('organization_name')); ?>" />
            <?php $__errorArgs = ['organization_name'];
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
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" placeholder="John Doe" aria-describedby="name" tabindex="2" value="<?php echo e(old('name')); ?>" />
            <?php $__errorArgs = ['name'];
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
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" placeholder="admin@example.com" aria-describedby="email" tabindex="3" value="<?php echo e(old('email')); ?>" />
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
            <small class="form-text text-muted">This will be your login email and organization contact email.</small>
          </div>
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" tabindex="4" />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
            <?php $__errorArgs = ['password'];
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
            <small class="form-text text-muted">Minimum 8 characters</small>
          </div>
          <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge" id="password_confirmation" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password_confirmation" tabindex="5" />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input class="custom-control-input" type="checkbox" id="register-privacy-policy" tabindex="6" />
              <label class="custom-control-label" for="register-privacy-policy">
                I agree to the <a href="<?php echo e(route('legal.terms')); ?>" target="_blank">Terms and Conditions</a> and <a href="<?php echo e(route('legal.privacy')); ?>" target="_blank">Privacy Policy</a>
              </label>
            </div>
          </div>
          <div class="alert alert-info border-left-3 border-left-info mb-2 shadow-sm">
            <div class="alert-body">
              <div class="d-flex align-items-center">
                <i data-feather="info" class="font-medium-3 mr-2"></i>
                <div>
                  <strong class="d-block mb-50">Important:</strong>
                  <span>After registration, you'll need to complete your annual subscription to start using Tracklet.</span>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block" tabindex="7">Register Organization</button>
        </form>

        <p class="text-center mt-2">
          <span>Already have an account?</span>
          <a href="<?php echo e(route('login')); ?>">
            <span>Sign in instead</span>
          </a>
        </p>
      </div>
    </div>
    <!-- /Register v1 -->
  </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/fullLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/auth/register-organization.blade.php ENDPATH**/ ?>