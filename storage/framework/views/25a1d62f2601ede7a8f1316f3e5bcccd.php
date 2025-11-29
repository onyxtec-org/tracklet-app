<body class="vertical-layout vertical-menu-modern <?php echo e($configData['showMenu'] === true ? '2-columns' : '1-column'); ?>

<?php echo e($configData['blankPageClass']); ?> <?php echo e($configData['bodyClass']); ?>

<?php echo e($configData['verticalMenuNavbarType']); ?>

<?php echo e($configData['sidebarClass']); ?> <?php echo e($configData['footerType']); ?>" data-menu="vertical-menu-modern" data-col="<?php echo e($configData['showMenu'] === true ? '2-columns' : '1-column'); ?>" data-layout="<?php echo e(($configData['theme'] === 'light') ? '' : $configData['layoutTheme']); ?>" style="<?php echo e($configData['bodyStyle']); ?>" data-framework="laravel" data-asset-path="<?php echo e(asset('/')); ?>">

  
  <?php if((isset($configData['showMenu']) && $configData['showMenu'] === true)): ?>
  <?php echo $__env->make('panels.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php endif; ?>

  
  <?php echo $__env->make('panels.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <!-- BEGIN: Content-->
  <div class="app-content content <?php echo e($configData['pageClass']); ?>">
    <!-- BEGIN: Header-->
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <?php if(($configData['contentLayout']!=='default') && isset($configData['contentLayout'])): ?>
    <div class="content-area-wrapper <?php echo e($configData['layoutWidth'] === 'boxed' ? 'container p-0' : ''); ?>">
      <div class="<?php echo e($configData['sidebarPositionClass']); ?>">
        <div class="sidebar">
          
          <?php echo $__env->yieldContent('content-sidebar'); ?>
        </div>
      </div>
      <div class="<?php echo e($configData['contentsidebarClass']); ?>">
        <div class="content-wrapper">
          <div class="content-body">
            
            <?php echo $__env->yieldContent('content'); ?>
          </div>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="content-wrapper <?php echo e($configData['layoutWidth'] === 'boxed' ? 'container p-0' : ''); ?>">
      
      <?php if($configData['pageHeader'] === true && isset($configData['pageHeader'])): ?>
      <?php echo $__env->make('panels.breadcrumb', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <?php endif; ?>

      <div class="content-body">
        
        <?php echo $__env->yieldContent('content'); ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
  <!-- End: Content-->

  <div class="sidenav-overlay"></div>
  <div class="drag-target"></div>

  
  <?php echo $__env->make('panels/footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  
  <?php echo $__env->make('panels/scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <script type="text/javascript">
    $(window).on('load', function() {
      if (feather) {
        feather.replace({
          width: 14
          , height: 14
        });
      }
    })

  </script>
</body>

</html>
<?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/layouts/verticalLayoutMaster.blade.php ENDPATH**/ ?>