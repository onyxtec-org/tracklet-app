<div class="content-header row">
  <div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
      <div class="col-12">
        <h2 class="content-header-title float-left mb-0"><?php echo $__env->yieldContent('title'); ?></h2>
        <div class="breadcrumb-wrapper">
          <?php if(@isset($breadcrumbs)): ?>
          <ol class="breadcrumb">
              
              <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="breadcrumb-item">
                  <?php if(isset($breadcrumb['link'])): ?>
                  <a href="<?php echo e($breadcrumb['link'] == 'javascript:void(0)' ? $breadcrumb['link']:url($breadcrumb['link'])); ?>">
                      <?php endif; ?>
                      <?php echo e($breadcrumb['name']); ?>

                      <?php if(isset($breadcrumb['link'])): ?>
                  </a>
                  <?php endif; ?>
              </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ol>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/panels/breadcrumb.blade.php ENDPATH**/ ?>