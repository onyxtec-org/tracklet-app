<?php
$configData = Helper::applClasses();
$user = auth()->user();
$canAccessMenu = false;

// Check if user can access menu items
if ($user) {
    // Super admin always has access
    if ($user->isSuperAdmin()) {
        $canAccessMenu = true;
    } 
    // Regular users need to belong to a subscribed organization
    elseif ($user->organization && $user->organization->isSubscribed()) {
        $canAccessMenu = true;
    }
}
?>

<?php if($canAccessMenu): ?>
<style>
  .navigation-header {
    padding: 4px 22px !important;
    margin-top: 0.5rem !important;
    margin-bottom: 0.25rem !important;
    border-top: 1px solid rgba(0,0,0,0.1) !important;
    padding-top: 0.75rem !important;
    text-align: left !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
  }
  .menu-dark .navigation-header {
    border-top-color: rgba(255,255,255,0.1) !important;
  }
  .navigation-header > i[data-feather="more-horizontal"] {
    display: none !important;
  }
  .navigation-header span {
    display: flex !important;
    align-items: center !important;
    font-weight: 600 !important;
    font-size: 0.85rem !important;
    text-align: left !important;
    justify-content: flex-start !important;
    width: 100% !important;
  }
  .navigation-header .header-icon {
    margin-right: 0.75rem;
    width: 14px;
    height: 14px;
    flex-shrink: 0;
  }
  .navbar-brand {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem 0.5rem;
    margin-top: 0 !important;
  }
  .navbar-brand .brand-logo img {
    max-width: 120px !important;
    max-height: 125px;
    height: auto;
    width: auto;
    object-fit: contain;
    display: block;
    padding: 0.5rem;
  }
</style>
<div class="main-menu menu-fixed <?php echo e((($configData['theme'] === 'dark') || ($configData['theme'] === 'semi-dark')) ? 'menu-dark' : 'menu-light'); ?> menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto"><a class="navbar-brand" href="<?php echo e(url('/')); ?>"><span class="brand-logo">
            <img src="<?php echo e(asset('images/logo/LOGO.svg')); ?>" alt="TrackLet"></span>
        </a></li>
      <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" href="javascript:void(0);" onclick="return false;"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      
      <?php if(isset($menuData[0])): ?>
      <?php
      // First pass: determine visibility for all menu items
      $navHeaders = [];
      $currentNavHeader = null;
      $menuItemMap = []; // Map to store menu items by their navheader
      
      foreach ($menuData[0]->menu as $index => $menu) {
        if (isset($menu->navheader)) {
          $currentNavHeader = $menu;
          $navHeaders[] = $currentNavHeader;
          if (!isset($menuItemMap[$index])) {
            $menuItemMap[$index] = [];
          }
        } else {
          $showMenu = true;
          
          // Check role-based visibility
          if (isset($menu->roles) && $user) {
            // Special handling for Super Admin: only show items with ONLY super_admin role
            if ($user->isSuperAdmin()) {
              // Super Admin should only see menu items that have ONLY super_admin role
              $showMenu = count($menu->roles) === 1 && $menu->roles[0] === 'super_admin';
            } else {
              // Regular users: check if they have any of the required roles
              $showMenu = false;
              foreach ($menu->roles as $role) {
                if ($user->hasRole($role)) {
                  $showMenu = true;
                  break;
                }
              }
              
              // Special case: "View Only" menu items should ONLY be visible to general_staff
              if (isset($menu->slug) && strpos($menu->slug, 'view.') === 0) {
                $showMenu = $user->hasRole('general_staff');
              }
            }
          } elseif (isset($menu->role) && $user) {
            // Backward compatibility with single role
            if ($user->isSuperAdmin()) {
              // Super Admin should only see items with super_admin role
              $showMenu = $menu->role === 'super_admin';
            } else {
              $showMenu = $user->hasRole($menu->role);
            }
          }
          
          // Store visibility on the menu object
          $menu->shouldShow = $showMenu;
          $menu->navHeader = $currentNavHeader;
          
          // Store menu item under its navheader
          if ($currentNavHeader) {
            $navHeaderIndex = array_search($currentNavHeader, $navHeaders);
            if (!isset($menuItemMap[$navHeaderIndex])) {
              $menuItemMap[$navHeaderIndex] = [];
            }
            $menuItemMap[$navHeaderIndex][] = $menu;
          }
        }
      }
      
      // Determine which navheaders should be shown (only if they have visible items after them)
      $visibleNavHeaders = [];
      foreach ($navHeaders as $navHeaderIndex => $navHeader) {
        $hasVisibleItems = false;
        
        // For Super Admin: navheaders should only show if they have super_admin-only items
        if ($user && $user->isSuperAdmin()) {
          // Super Admin should not see any navheaders (only standalone items like Organizations)
          $hasVisibleItems = false;
        } else {
          // Regular users: check if navheader has visible items
          // Special case: "View Only" navheader should only show for general_staff
          if (isset($navHeader->navheader) && $navHeader->navheader === 'View Only') {
            // Check if user is general_staff
            if ($user && $user->hasRole('general_staff')) {
              // Check if any menu items under this navheader are visible
              if (isset($menuItemMap[$navHeaderIndex])) {
                foreach ($menuItemMap[$navHeaderIndex] as $item) {
                  if ($item->shouldShow) {
                    $hasVisibleItems = true;
                    break;
                  }
                }
              }
            }
          } else {
            // Check if any menu items under this navheader are visible
            if (isset($menuItemMap[$navHeaderIndex])) {
              foreach ($menuItemMap[$navHeaderIndex] as $item) {
                if ($item->shouldShow) {
                  $hasVisibleItems = true;
                  break;
                }
              }
            }
          }
        }
        
        if ($hasVisibleItems) {
          $visibleNavHeaders[] = $navHeader;
        }
      }
      ?>
      
      <?php $__currentLoopData = $menuData[0]->menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if(isset($menu->navheader)): ?>
      <?php if(in_array($menu, $visibleNavHeaders)): ?>
      <?php
      // Map navheader names to icons
      $headerIcons = [
        'Dashboard' => 'home',
        'Administration' => 'settings',
        'Finance' => 'dollar-sign',
        'Operations' => 'briefcase',
        'Inventory' => 'package',
        'Assets' => 'layers'
      ];
      $headerName = $menu->navheader;
      $headerIcon = isset($headerIcons[$headerName]) ? $headerIcons[$headerName] : 'folder';
      ?>
      <li class="navigation-header">
        <span><?php echo e($menu->navheader); ?></span>
        <i data-feather="more-horizontal"></i>
      </li>
      <?php endif; ?>
      <?php else: ?>
      <?php
      // Use pre-calculated visibility from first pass
      $showMenu = isset($menu->shouldShow) ? $menu->shouldShow : true;
      ?>

      <?php if($showMenu): ?>
      
      <?php
      $custom_classes = "";
      if(isset($menu->classlist)) {
      $custom_classes = $menu->classlist;
      }
      ?>
      <li class="nav-item <?php echo e(request()->routeIs($menu->slug.'*') ? 'active' : ''); ?> <?php echo e($custom_classes); ?>">
        <a href="<?php echo e(isset($menu->url) ? url($menu->url) : 'javascript:void(0)'); ?>" class="d-flex align-items-center" target="<?php echo e(isset($menu->newTab) ? '_blank' : '_self'); ?>">
          <i data-feather="<?php echo e($menu->icon); ?>"></i>
          <span class="menu-title text-truncate"><?php echo e(__('locale.'.$menu->name)); ?></span>
          <?php if(isset($menu->badge)): ?>
          <?php $badgeClasses = "badge badge-pill badge-light-primary ml-auto mr-1" ?>
          <span class="<?php echo e(isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses); ?>"><?php echo e($menu->badge); ?></span>
          <?php endif; ?>
        </a>
        <?php if(isset($menu->submenu)): ?>
        <?php echo $__env->make('panels/submenu', ['menu' => $menu->submenu], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
      </li>
      <?php endif; ?>
      <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
      
    </ul>
  </div>
</div>
<?php endif; ?>
<!-- END: Main Menu-->
<script>
  $(document).ready(function() {
    // Prevent the navbar toggle from collapsing the menu
    $('.modern-nav-toggle').off('click').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      return false;
    });
  });
</script><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/panels/sidebar.blade.php ENDPATH**/ ?>