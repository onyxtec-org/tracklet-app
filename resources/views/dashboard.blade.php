
@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap.min.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-invoice-list.css')) }}">
  @endsection

@section('content')
<!-- Dashboard Analytics Start -->
<section id="dashboard-analytics">
  @if(!auth()->user()->isSuperAdmin() && isset($trialInfo) && $trialInfo['is_on_trial'])
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
                  @if($trialInfo['trial_days_remaining'] > 0)
                    <strong>{{ $trialInfo['trial_days_remaining'] }}</strong> {{ $trialInfo['trial_days_remaining'] == 1 ? 'day' : 'days' }} remaining. 
                    Trial ends on {{ $trialInfo['trial_ends_at']->format('M j, Y') }}. 
                    Your annual subscription will begin automatically after the trial.
                  @else
                    Your trial ends today. Your annual subscription will begin automatically.
                  @endif
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
  @endif

  <div class="row match-height">
    <!-- Greetings Card starts -->
    <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="card card-congratulations">
        <div class="card-body text-center">
          <img
            src="{{asset('images/elements/decore-left.png')}}"
            class="congratulations-img-left"
            alt="card-img-left"
          />
          <img
            src="{{asset('images/elements/decore-right.png')}}"
            class="congratulations-img-right"
            alt="card-img-right"
          />
          <div class="avatar avatar-xl bg-primary shadow">
            <div class="avatar-content">
              <i data-feather="award" class="font-large-1"></i>
            </div>
          </div>
          <div class="text-center">
            <h1 class="mb-1 text-white">Welcome {{ auth()->user()->name }},</h1>
            <p class="card-text m-auto w-75">
              @if(auth()->user()->isSuperAdmin())
                Super Admin Dashboard - Manage all organizations and monitor system-wide statistics.
              @elseif(isset($trialInfo) && $trialInfo['is_on_trial'])
                You're currently on a <strong>free trial</strong>. Enjoy full access to all Tracklet features!
              @else
                Welcome to your Tracklet dashboard. Manage your organization and track your activities.
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>
    <!-- Greetings Card ends -->

    @if(auth()->user()->isSuperAdmin() && isset($superAdminStats))
      <!-- Total Organizations Card -->
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-header flex-column align-items-start pb-0">
            <div class="avatar bg-light-primary p-50 m-0">
              <div class="avatar-content">
                <i data-feather="briefcase" class="font-medium-5"></i>
              </div>
            </div>
            <h2 class="font-weight-bolder mt-1">{{ number_format($superAdminStats['total_organizations']) }}</h2>
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
            <h2 class="font-weight-bolder mt-1">{{ number_format($superAdminStats['subscribed_organizations']) }}</h2>
            <p class="card-text">Active Subscriptions</p>
          </div>
          <div class="card-body">
            <small class="text-muted">{{ $superAdminStats['trial_organizations'] }} on trial</small>
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
            <h2 class="font-weight-bolder mt-1">{{ number_format($superAdminStats['total_users']) }}</h2>
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
            <h2 class="font-weight-bolder mt-1">{{ number_format($superAdminStats['pending_invitations']) }}</h2>
            <p class="card-text">Pending Invitations</p>
          </div>
          <div class="card-body">
            <small class="text-muted">{{ $superAdminStats['expired_invitations'] }} expired</small>
          </div>
        </div>
      </div>
    @else
      <!-- Organization Dashboard Stats Cards -->
      @if(isset($financialSnapshot))
        <!-- Financial Snapshot Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-primary p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="dollar-sign" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1">${{ number_format($financialSnapshot['current_month'], 2) }}</h2>
              <p class="card-text">This Month Expenses</p>
            </div>
            <div class="card-body">
              @if($financialSnapshot['change'] != 0)
                <small class="{{ $financialSnapshot['change'] > 0 ? 'text-danger' : 'text-success' }}">
                  {{ $financialSnapshot['change'] > 0 ? '+' : '' }}{{ number_format($financialSnapshot['change'], 1) }}% vs last month
                </small>
              @else
                <small class="text-muted">No previous month data</small>
              @endif
            </div>
          </div>
        </div>
      @endif

      @if(isset($inventoryStatus))
        <!-- Low Stock Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-warning p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="alert-triangle" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1">{{ $inventoryStatus['low_stock_count'] }}</h2>
              <p class="card-text">Low Stock Items</p>
            </div>
            <div class="card-body">
              <small class="text-muted">Items below threshold</small>
            </div>
          </div>
        </div>
      @endif

      @if(isset($assetSummary))
        <!-- Assets Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-info p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="package" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1">{{ $assetSummary['total'] }}</h2>
              <p class="card-text">Total Assets</p>
            </div>
            <div class="card-body">
              <small class="text-muted">{{ $assetSummary['active'] }} active, {{ $assetSummary['in_repair'] }} in repair</small>
            </div>
          </div>
        </div>
      @endif

      @if(isset($upcomingMaintenance))
        <!-- Upcoming Maintenance Card -->
        <div class="col-lg-3 col-sm-6 col-12">
          <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
              <div class="avatar bg-light-danger p-50 m-0">
                <div class="avatar-content">
                  <i data-feather="tool" class="font-medium-5"></i>
                </div>
              </div>
              <h2 class="font-weight-bolder mt-1">{{ $upcomingMaintenance->count() }}</h2>
              <p class="card-text">Upcoming Maintenance</p>
            </div>
            <div class="card-body">
              <small class="text-muted">Next 7 days</small>
            </div>
          </div>
        </div>
      @endif
    @endif
  </div>

  @if(auth()->user()->isSuperAdmin() && isset($superAdminStats))
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
                    <h3 class="mb-0">{{ $superAdminStats['active_subscriptions'] }}</h3>
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
                    <h3 class="mb-0">{{ $superAdminStats['trial_organizations'] }}</h3>
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
                    <h3 class="mb-0">{{ $superAdminStats['total_organizations'] - $superAdminStats['subscribed_organizations'] }}</h3>
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
                      {{ $superAdminStats['total_organizations'] > 0 ? number_format(($superAdminStats['subscribed_organizations'] / $superAdminStats['total_organizations']) * 100, 1) : 0 }}%
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
                    <h3 class="mb-0">{{ $superAdminStats['organizations_by_source']['invited'] }}</h3>
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
                    <h3 class="mb-0">{{ $superAdminStats['organizations_by_source']['self_registered'] }}</h3>
                    <small class="text-muted">Self-Registered</small>
                  </div>
                </div>
              </div>
              <div class="col-12 mt-2">
                <div class="progress" style="height: 8px;">
                  @php
                    $total = $superAdminStats['organizations_by_source']['invited'] + $superAdminStats['organizations_by_source']['self_registered'];
                    $invitedPercent = $total > 0 ? ($superAdminStats['organizations_by_source']['invited'] / $total) * 100 : 0;
                    $selfRegisteredPercent = $total > 0 ? ($superAdminStats['organizations_by_source']['self_registered'] / $total) * 100 : 0;
                  @endphp
                  <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $invitedPercent }}%"></div>
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ $selfRegisteredPercent }}%"></div>
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
            <a href="{{ route('superadmin.organizations.index') }}" class="btn btn-sm btn-primary">View All</a>
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
                  @forelse($superAdminStats['recent_organizations'] as $org)
                    <tr>
                      <td>
                        <strong>{{ $org->name }}</strong>
                      </td>
                      <td>{{ $org->email }}</td>
                      <td>
                        <span class="badge badge-{{ $org->registration_source === 'invited' ? 'primary' : 'success' }}">
                          {{ ucfirst(str_replace('_', ' ', $org->registration_source)) }}
                        </span>
                      </td>
                      <td>
                        @if($org->isSubscribed())
                          <span class="badge badge-success">Subscribed</span>
                        @elseif($org->isOnTrial())
                          <span class="badge badge-info">Trial ({{ $org->trialDaysRemaining() }}d)</span>
                        @else
                          <span class="badge badge-warning">Not Subscribed</span>
                        @endif
                      </td>
                      <td>{{ $org->users()->count() }}</td>
                      <td>{{ $org->created_at->format('M d, Y') }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center">No organizations yet</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <!-- Organization Dashboard Content (existing content for non-super-admin users) -->
    <div class="row match-height">
      @if(isset($financialSnapshot) && isset($expenseCharts))
        <!-- Financial Charts Card -->
        <div class="col-lg-6 col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Expense Overview</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 mb-2">
                  <h3 class="mb-0">${{ number_format($financialSnapshot['current_month'], 2) }}</h3>
                  <small class="text-muted">Current Month Expenses</small>
                  @if($financialSnapshot['change'] != 0)
                    <div class="mt-1">
                      <span class="badge badge-{{ $financialSnapshot['change'] > 0 ? 'danger' : 'success' }}">
                        {{ $financialSnapshot['change'] > 0 ? '+' : '' }}{{ number_format($financialSnapshot['change'], 1) }}% vs last month
                      </span>
                    </div>
                  @endif
                </div>
                @if(count($financialSnapshot['top_categories']) > 0)
                  <div class="col-12 mt-2">
                    <h6>Top Categories This Month:</h6>
                    <ul class="list-unstyled">
                      @foreach($financialSnapshot['top_categories']->take(5) as $cat)
                        <li class="mb-1">
                          <span class="font-weight-bold">{{ $cat['category'] }}:</span>
                          ${{ number_format($cat['amount'], 2) }}
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endif

      @if(isset($inventoryStatus) && $inventoryStatus['low_stock_count'] > 0)
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
                    @foreach($inventoryStatus['low_stock_items']->take(5) as $item)
                      <tr>
                        <td>{{ $item->name }}</td>
                        <td><span class="badge badge-danger">{{ $item->quantity }}</span></td>
                        <td>{{ $item->minimum_threshold }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @if($inventoryStatus['low_stock_count'] > 5)
                <a href="{{ route('inventory.low-stock') }}" class="btn btn-sm btn-warning btn-block mt-1">
                  View All {{ $inventoryStatus['low_stock_count'] }} Low Stock Items
                </a>
              @endif
            </div>
          </div>
        </div>
      @endif
    </div>

    @if(isset($upcomingMaintenance) && $upcomingMaintenance->count() > 0)
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
                    @foreach($upcomingMaintenance as $maintenance)
                      <tr>
                        <td>{{ $maintenance->asset->name }}</td>
                        <td>{{ $maintenance->type }}</td>
                        <td>{{ $maintenance->scheduled_date->format('M d, Y') }}</td>
                        <td>
                          <span class="badge badge-{{ $maintenance->status === 'pending' ? 'warning' : 'info' }}">
                            {{ ucfirst($maintenance->status) }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  @endif

</section>
<!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/pages/dashboard-analytics.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/pages/app-invoice-list.js')) }}"></script>
@endsection
