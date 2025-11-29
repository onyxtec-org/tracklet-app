<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/vendors.min.css'))); ?>" />
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/ui/prism.min.css'))); ?>" />

<?php echo $__env->yieldContent('vendor-style'); ?>


<link rel="stylesheet" href="<?php echo e(asset(mix('css/core.css'))); ?>" />


<?php $configData = Helper::applClasses(); ?>


<?php if($configData['mainLayoutType'] === 'horizontal'): ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('css/base/core/menu/menu-types/horizontal-menu.css'))); ?>" />
<?php endif; ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('css/base/core/menu/menu-types/vertical-menu.css'))); ?>" />
<!-- <link rel="stylesheet" href="<?php echo e(asset(mix('css/base/core/colors/palette-gradient.css'))); ?>"> -->


<?php echo $__env->yieldContent('page-style'); ?>


<link rel="stylesheet" href="<?php echo e(asset(mix('css/overrides.css'))); ?>" />



<?php if($configData['direction'] === 'rtl' && isset($configData['direction'])): ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('css/custom-rtl.css'))); ?>" />
<?php endif; ?>


<link rel="stylesheet" href="<?php echo e(asset(mix('css/style.css'))); ?>" />
<link rel="stylesheet" href="<?php echo e(asset(mix('css/style-rtl.css'))); ?>" />
<?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/panels/styles.blade.php ENDPATH**/ ?>