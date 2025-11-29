<?php $__env->startSection('title', 'Dashboard Analytics'); ?>

<?php $__env->startSection('vendor-style'); ?>
  <!-- vendor css files -->
  <link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/charts/apexcharts.css'))); ?>">
  <link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/extensions/toastr.min.css'))); ?>">
  <link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/datatables.min.css'))); ?>">
  <link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/responsive.bootstrap.min.css'))); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-style'); ?>
  <!-- Page css files -->
  <link rel="stylesheet" href="<?php echo e(asset(mix('css/base/plugins/charts/chart-apex.css'))); ?>">
  <link rel="stylesheet" href="<?php echo e(asset(mix('css/base/plugins/extensions/ext-component-toastr.css'))); ?>">
  <link rel="stylesheet" href="<?php echo e(asset(mix('css/base/pages/app-invoice-list.css'))); ?>">
  <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Dashboard Analytics Start -->
<section id="dashboard-analytics">
  <?php if(!auth()->user()->isSuperAdmin() && isset($trialInfo) && $trialInfo['is_on_trial']): ?>
  <div class="row">
    <div class="col-12">
      <div class="alert alert-info border-left-3 border-left-info alert-dismissible fade show shadow-sm" role="alert">
        <div class="alert-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <i data-feather="gift" class="font-medium-3 mr-2 text-info"></i>
              <div>
                <h6 class="alert-heading mb-1 font-weight-bolder">Free Trial Active</h6>
                <p class="mb-0">
                  <?php if($trialInfo['trial_days_remaining'] > 0): ?>
                    <strong><?php echo e($trialInfo['trial_days_remaining']); ?></strong> <?php echo e($trialInfo['trial_days_remaining'] == 1 ? 'day' : 'days'); ?> remaining. 
                    Trial ends on <?php echo e($trialInfo['trial_ends_at']->format('M j, Y')); ?>. 
                    Your annual subscription will begin automatically after the trial.
                  <?php else: ?>
                    Your trial ends today. Your annual subscription will begin automatically.
                  <?php endif; ?>
                </p>
              </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row match-height">
    <!-- Greetings Card starts -->
    <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="card card-congratulations">
        <div class="card-body text-center">
          <img
            src="<?php echo e(asset('images/elements/decore-left.png')); ?>"
            class="congratulations-img-left"
            alt="card-img-left"
          />
          <img
            src="<?php echo e(asset('images/elements/decore-right.png')); ?>"
            class="congratulations-img-right"
            alt="card-img-right"
          />
          <div class="avatar avatar-xl bg-primary shadow">
            <div class="avatar-content">
              <i data-feather="award" class="font-large-1"></i>
            </div>
          </div>
          <div class="text-center">
            <h1 class="mb-1 text-white">Welcome <?php echo e(auth()->user()->name); ?>,</h1>
            <p class="card-text m-auto w-75">
              <?php if(auth()->user()->isSuperAdmin()): ?>
                Super Admin Dashboard - Manage all organizations and monitor system-wide statistics.
              <?php elseif(isset($trialInfo) && $trialInfo['is_on_trial']): ?>
                You're currently on a <strong>free trial</strong>. Enjoy full access to all Tracklet features!
              <?php else: ?>
                Welcome to your Tracklet dashboard. Manage your organization and track your activities.
              <?php endif; ?>
            </p>
          </div>
        </div>
      </div>
    </div>
    <!-- Greetings Card ends -->

    <?php if(auth()->user()->isSuperAdmin() && isset($superAdminStats)): ?>
      <!-- Total Organizations Card -->
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-header flex-column align-items-start pb-0">
            <div class="avatar bg-light-primary p-50 m-0">
              <div class="avatar-content">
                <i data-feather="briefcase" class="font-medium-5"></i>
              </div>
            </div>
            <h2 class="font-weight-bolder mt-1"><?php echo e(number_format($superAdminStats['total_organizations'])); ?></h2>
            <p class="card-text">Total Organizations</p>
          </div>
          <div class="card-body">
            <small class="text-muted">All registered organizations</small>
          </div>
        </div>
      </div>

      <!-- Subscribed Organizations Card -->
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-header flex-column align-items-start pb-0">
            <div class="avatar bg-light-success p-50 m-0">
              <div class="avatar-content">
                <i data-feather="check-circle" class="font-medium-5"></i>
              </div>
            </div>
            <h2 class="font-weight-bolder mt-1"><?php echo e(number_format($superAdminStats['subscribed_organizations'])); ?></h2>
            <p class="card-text">Active Subscriptions</p>
          </div>
          <div class="card-body">
            <small class="text-muted"><?php echo e($superAdminStats['trial_organizations']); ?> on trial</small>
          </div>
        </div>
      </div>

      <!-- Total Users Card -->
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-header flex-column align-items-start pb-0">
            <div class="avatar bg-light-info p-50 m-0">
              <div class="avatar-content">
                <i data-feather="users" class="font-medium-5"></i>
              </div>
            </div>
            <h2 class="font-weight-bolder mt-1"><?php echo e(number_format($superAdminStats['total_users'])); ?></h2>
            <p class="card-text">Total Users</p>
          </div>
          <div class="card-body">
            <small class="text-muted">Across all organizations</small>
          </div>
        </div>
      </div>

      <!-- Pending Invitations Card -->
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-header flex-column align-items-start pb-0">
            <div class="avatar bg-light-warning p-50 m-0">
              <div class="avatar-content">
                <i data-feather="mail" class="font-medium-5"></i>
              </div>
            </div>
            <h2 class="font-weight-bolder mt-1"><?php echo e(number_format($superAdminStats['pending_invitations'])); ?></h2>
            <p class="card-text">Pending Invitations</p>
          </div>
          <div class="card-body">
            <small class="text-muted"><?php echo e($superAdminStats['expired_invitations']); ?> expired</small>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Organization Dashboard Stats Cards -->
      <?php if(isset($financialSnapshot)): ?>
        <!-- Financial Snapshot Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-primary p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="dollar-sign" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1">$<?php echo e(number_format($financialSnapshot['current_month'], 2)); ?></h2>
              <p class="card-text">This Month Expenses</p>
            </div>
            <div class="card-body">
              <?php if($financialSnapshot['change'] != 0): ?>
                <small class="<?php echo e($financialSnapshot['change'] > 0 ? 'text-danger' : 'text-success'); ?>">
                  <?php echo e($financialSnapshot['change'] > 0 ? '+' : ''); ?><?php echo e(number_format($financialSnapshot['change'], 1)); ?>% vs last month
                </small>
              <?php else: ?>
                <small class="text-muted">No previous month data</small>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(isset($inventoryStatus)): ?>
        <!-- Low Stock Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-warning p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="alert-triangle" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1"><?php echo e($inventoryStatus['low_stock_count']); ?></h2>
              <p class="card-text">Low Stock Items</p>
            </div>
            <div class="card-body">
              <small class="text-muted">Items below threshold</small>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(isset($assetSummary)): ?>
        <!-- Assets Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-info p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="package" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1"><?php echo e($assetSummary['total']); ?></h2>
              <p class="card-text">Total Assets</p>
            </div>
            <div class="card-body">
              <small class="text-muted"><?php echo e($assetSummary['active']); ?> active, <?php echo e($assetSummary['in_repair']); ?> in repair</small>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(isset($upcomingMaintenance)): ?>
        <!-- Upcoming Maintenance Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-danger p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="tool" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1"><?php echo e($upcomingMaintenance->count()); ?></h2>
              <p class="card-text">Upcoming Maintenance</p>
            </div>
            <div class="card-body">
              <small class="text-muted">Next 7 days</small>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <?php if(auth()->user()->isSuperAdmin() && isset($superAdminStats)): ?>
    <!-- Super Admin Statistics Section -->
    <div class="row match-height">
      <!-- Subscription Overview Card -->
      <div class="col-lg-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Subscription Overview</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-6 mb-2">
                <div class="d-flex align-items-center">
                  <div class="avatar bg-light-success mr-1">
                    <div class="avatar-content">
                      <i data-feather="check-circle" class="font-medium-3"></i>
                    </div>
                  </div>
                  <div>
                    <h3 class="mb-0"><?php echo e($superAdminStats['active_subscriptions']); ?></h3>
                    <small class="text-muted">Active Subscriptions</small>
                  </div>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="d-flex align-items-center">
                  <div class="avatar bg-light-info mr-1">
                    <div class="avatar-content">
                      <i data-feather="clock" class="font-medium-3"></i>
                    </div>
                  </div>
                  <div>
                    <h3 class="mb-0"><?php echo e($superAdminStats['trial_organizations']); ?></h3>
                    <small class="text-muted">On Trial</small>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="avatar bg-light-warning mr-1">
                    <div class="avatar-content">
                      <i data-feather="x-circle" class="font-medium-3"></i>
                    </div>
                  </div>
                  <div>
                    <h3 class="mb-0"><?php echo e($superAdminStats['total_organizations'] - $superAdminStats['subscribed_organizations']); ?></h3>
                    <small class="text-muted">Not Subscribed</small>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="avatar bg-light-primary mr-1">
                    <div class="avatar-content">
                      <i data-feather="percent" class="font-medium-3"></i>
                    </div>
                  </div>
                  <div>
                    <h3 class="mb-0">
                      <?php echo e($superAdminStats['total_organizations'] > 0 ? number_format(($superAdminStats['subscribed_organizations'] / $superAdminStats['total_organizations']) * 100, 1) : 0); ?>%
                    </h3>
                    <small class="text-muted">Subscription Rate</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Registration Source Card -->
      <div class="col-lg-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Registration Sources</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-6 mb-2">
                <div class="d-flex align-items-center">
                  <div class="avatar bg-light-primary mr-1">
                    <div class="avatar-content">
                      <i data-feather="mail" class="font-medium-3"></i>
                    </div>
                  </div>
                  <div>
                    <h3 class="mb-0"><?php echo e($superAdminStats['organizations_by_source']['invited']); ?></h3>
                    <small class="text-muted">Invited</small>
                  </div>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="d-flex align-items-center">
                  <div class="avatar bg-light-success mr-1">
                    <div class="avatar-content">
                      <i data-feather="user-plus" class="font-medium-3"></i>
                    </div>
                  </div>
                  <div>
                    <h3 class="mb-0"><?php echo e($superAdminStats['organizations_by_source']['self_registered']); ?></h3>
                    <small class="text-muted">Self-Registered</small>
                  </div>
                </div>
              </div>
              <div class="col-12 mt-2">
                <div class="progress" style="height: 8px;">
                  <?php
                    $total = $superAdminStats['organizations_by_source']['invited'] + $superAdminStats['organizations_by_source']['self_registered'];
                    $invitedPercent = $total > 0 ? ($superAdminStats['organizations_by_source']['invited'] / $total) * 100 : 0;
                    $selfRegisteredPercent = $total > 0 ? ($superAdminStats['organizations_by_source']['self_registered'] / $total) * 100 : 0;
                  ?>
                  <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo e($invitedPercent); ?>%"></div>
                  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($selfRegisteredPercent); ?>%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Organizations Table -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Recent Organizations</h4>
            <a href="<?php echo e(route('superadmin.organizations.index')); ?>" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Organization</th>
                    <th>Email</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Users</th>
                    <th>Created</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__empty_1 = true; $__currentLoopData = $superAdminStats['recent_organizations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                      <td>
                        <strong><?php echo e($org->name); ?></strong>
                      </td>
                      <td><?php echo e($org->email); ?></td>
                      <td>
                        <span class="badge badge-<?php echo e($org->registration_source === 'invited' ? 'primary' : 'success'); ?>">
                          <?php echo e(ucfirst(str_replace('_', ' ', $org->registration_source))); ?>

                        </span>
                      </td>
                      <td>
                        <?php if($org->isSubscribed()): ?>
                          <span class="badge badge-success">Subscribed</span>
                        <?php elseif($org->isOnTrial()): ?>
                          <span class="badge badge-info">Trial (<?php echo e($org->trialDaysRemaining()); ?>d)</span>
                        <?php else: ?>
                          <span class="badge badge-warning">Not Subscribed</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo e($org->users()->count()); ?></td>
                      <td><?php echo e($org->created_at->format('M d, Y')); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                      <td colspan="6" class="text-center">No organizations yet</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <!-- Organization Dashboard Content (existing content for non-super-admin users) -->
    <div class="row match-height">
      <?php if(isset($financialSnapshot) && isset($expenseCharts)): ?>
        <!-- Financial Charts Card -->
        <div class="col-lg-6 col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Expense Overview</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 mb-2">
                  <h3 class="mb-0">$<?php echo e(number_format($financialSnapshot['current_month'], 2)); ?></h3>
                  <small class="text-muted">Current Month Expenses</small>
                  <?php if($financialSnapshot['change'] != 0): ?>
                    <div class="mt-1">
                      <span class="badge badge-<?php echo e($financialSnapshot['change'] > 0 ? 'danger' : 'success'); ?>">
                        <?php echo e($financialSnapshot['change'] > 0 ? '+' : ''); ?><?php echo e(number_format($financialSnapshot['change'], 1)); ?>% vs last month
                      </span>
                    </div>
                  <?php endif; ?>
                </div>
                <?php if(count($financialSnapshot['top_categories']) > 0): ?>
                  <div class="col-12 mt-2">
                    <h6>Top Categories This Month:</h6>
                    <ul class="list-unstyled">
                      <?php $__currentLoopData = $financialSnapshot['top_categories']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="mb-1">
                          <span class="font-weight-bold"><?php echo e($cat['category']); ?>:</span>
                          $<?php echo e(number_format($cat['amount'], 2)); ?>

                        </li>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(isset($inventoryStatus) && $inventoryStatus['low_stock_count'] > 0): ?>
        <!-- Low Stock Items Card -->
        <div class="col-lg-6 col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Low Stock Alerts</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Item</th>
                      <th>Current Stock</th>
                      <th>Threshold</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $__currentLoopData = $inventoryStatus['low_stock_items']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($item->name); ?></td>
                        <td><span class="badge badge-danger"><?php echo e($item->quantity); ?></span></td>
                        <td><?php echo e($item->minimum_threshold); ?></td>
                      </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
                </table>
              </div>
              <?php if($inventoryStatus['low_stock_count'] > 5): ?>
                <a href="<?php echo e(route('inventory.low-stock')); ?>" class="btn btn-sm btn-warning btn-block mt-1">
                  View All <?php echo e($inventoryStatus['low_stock_count']); ?> Low Stock Items
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?php if(isset($upcomingMaintenance) && $upcomingMaintenance->count() > 0): ?>
      <!-- Upcoming Maintenance Card -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Upcoming Maintenance (Next 7 Days)</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Asset</th>
                      <th>Type</th>
                      <th>Scheduled Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $__currentLoopData = $upcomingMaintenance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $maintenance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($maintenance->asset ? $maintenance->asset->name : 'N/A (Asset Deleted)'); ?></td>
                        <td><?php echo e($maintenance->type); ?></td>
                        <td><?php echo e($maintenance->scheduled_date->format('M d, Y')); ?></td>
                        <td>
                          <span class="badge badge-<?php echo e($maintenance->status === 'pending' ? 'warning' : 'info'); ?>">
                            <?php echo e(ucfirst($maintenance->status)); ?>

                          </span>
                        </td>
                      </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

</section>
<!-- Dashboard Analytics end -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
  <!-- vendor files -->
  <script src="<?php echo e(asset(mix('vendors/js/charts/apexcharts.min.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('vendors/js/extensions/toastr.min.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('vendors/js/extensions/moment.min.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('vendors/js/tables/datatable/datatables.min.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('vendors/js/tables/datatable/responsive.bootstrap.min.js'))); ?>"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
  <!-- Page js files -->
  <script src="<?php echo e(asset(mix('js/scripts/pages/dashboard-analytics.js'))); ?>"></script>
  <script src="<?php echo e(asset(mix('js/scripts/pages/app-invoice-list.js'))); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/dashboard.blade.php ENDPATH**/ ?>