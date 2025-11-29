<?php if(isset($pageConfigs)): ?>
<?php echo Helper::updatePageConfig($pageConfigs); ?>

<?php endif; ?>

<!DOCTYPE html>

<?php
$configData = Helper::applClasses();
?>
<html lang="<?php if(session()->has('locale')): ?><?php echo e(session()->get('locale')); ?><?php else: ?><?php echo e($configData['defaultLanguage']); ?><?php endif; ?>" data-textdirection="<?php echo e(env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr'); ?>" class="<?php echo e(($configData['theme'] === 'light') ? '' : $configData['layoutTheme']); ?>">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e(config('app.name')); ?> </title>
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('images/logo/MKfavicon.ico')); ?>">

  
  <?php echo $__env->make('panels/styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  
  <?php echo $__env->make('panels/styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>



<body class="vertical-layout vertical-menu-modern <?php echo e($configData['blankPageClass']); ?> <?php echo e($configData['bodyClass']); ?> <?php echo e(($configData['theme'] === 'dark') ? 'dark-layout' : 'light'); ?>

    data-menu=" vertical-menu-modern" data-layout="<?php echo e(($configData['theme'] === 'light') ? '' : $configData['layoutTheme']); ?>" style="<?php echo e($configData['bodyStyle']); ?>" data-framework="laravel" data-asset-path="<?php echo e(asset('/')); ?>">

  <!-- BEGIN: Content-->
  <div class="app-content content <?php echo e($configData['pageClass']); ?>">
    <div class="content-wrapper <?php echo e($configData['layoutWidth'] === 'boxed' ? 'container p-0' : ''); ?>">
      <div class="content-body">

        
        <?php echo $__env->yieldContent('content'); ?>

      </div>
    </div>
  </div>
  <!-- End: Content-->

  
  <?php echo $__env->make('panels/scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <script type="text/javascript">
    $(window).on('load', function() {
      if (feather) {
        feather.replace({
          width: 14,
          height: 14
        });
      }
    })
  </script>
</body>

</html><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/layouts/fullLayoutMaster.blade.php ENDPATH**/ ?>