@php
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
@endphp

@if($canAccessMenu)
<div class="main-menu menu-fixed {{(($configData['theme'] === 'dark') || ($configData['theme'] === 'semi-dark')) ? 'menu-dark' : 'menu-light'}} menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto"><a class="navbar-brand" href="{{url('/')}}"><span class="brand-logo">
            <img src="{{asset('images/logo/MKfavicon.ico')}}" alt="TrackLet"></span>
          <h2 class="brand-text">TrackLet</h2>
        </a></li>
      <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      {{-- Foreach menu item starts --}}
      @if(isset($menuData[0]))
      @php
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
            $showMenu = false;
            foreach ($menu->roles as $role) {
              if ($user->hasRole($role)) {
                $showMenu = true;
                break;
              }
            }
            
            // Special case: "View Only" menu items should ONLY be visible to general_staff
            // Super Admin should NOT see these items
            if (isset($menu->slug) && strpos($menu->slug, 'view.') === 0) {
              if ($user->isSuperAdmin()) {
                $showMenu = false;
              } else {
                // Only show if user is general_staff
                $showMenu = $user->hasRole('general_staff');
              }
            }
          } elseif (isset($menu->role) && $user) {
            // Backward compatibility with single role
            $showMenu = $user->hasRole($menu->role);
          }
          
          // Super admin can see everything EXCEPT "View Only" items
          if ($user && $user->isSuperAdmin()) {
            // Don't show "View Only" items to Super Admin
            if (!isset($menu->slug) || strpos($menu->slug, 'view.') !== 0) {
              $showMenu = true;
            } else {
              $showMenu = false;
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
        
        // Special case: "View Only" navheader should only show for general_staff
        if (isset($navHeader->navheader) && $navHeader->navheader === 'View Only') {
          if ($user && $user->isSuperAdmin()) {
            $hasVisibleItems = false; // Super Admin should not see "View Only" section
          } else {
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
        
        if ($hasVisibleItems) {
          $visibleNavHeaders[] = $navHeader;
        }
      }
      @endphp
      
      @foreach($menuData[0]->menu as $menu)
      @if(isset($menu->navheader))
      @if(in_array($menu, $visibleNavHeaders))
      <li class="navigation-header">
        <span>{{ __('locale.'.$menu->navheader) }}</span>
        <i data-feather="more-horizontal"></i>
      </li>
      @endif
      @else
      @php
      // Use pre-calculated visibility from first pass
      $showMenu = isset($menu->shouldShow) ? $menu->shouldShow : true;
      @endphp

      @if($showMenu)
      {{-- Add Custom Class with nav-item --}}
      @php
      $custom_classes = "";
      if(isset($menu->classlist)) {
      $custom_classes = $menu->classlist;
      }
      @endphp
      <li class="nav-item {{ request()->routeIs($menu->slug.'*') ? 'active' : '' }} {{ $custom_classes }}">
        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0)' }}" class="d-flex align-items-center" target="{{ isset($menu->newTab) ? '_blank' : '_self' }}">
          <i data-feather="{{ $menu->icon }}"></i>
          <span class="menu-title text-truncate">{{ __('locale.'.$menu->name) }}</span>
          @if (isset($menu->badge))
          <?php $badgeClasses = "badge badge-pill badge-light-primary ml-auto mr-1" ?>
          <span class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }}">{{$menu->badge}}</span>
          @endif
        </a>
        @if(isset($menu->submenu))
        @include('panels/submenu', ['menu' => $menu->submenu])
        @endif
      </li>
      @endif
      @endif
      @endforeach
      @endif
      {{-- Foreach menu item ends --}}
    </ul>
  </div>
</div>
@endif
<!-- END: Main Menu-->