<?php $__env->startSection('title', 'Organizations'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css'))); ?>">
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css'))); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Organizations</h4>
        <a href="<?php echo e(route('superadmin.organizations.create')); ?>" class="btn btn-primary">
            <i data-feather="plus" class="mr-1"></i> Invite Organization
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table organizations-table" id="organizations-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Invitation Status</th>
                        <th>Subscription</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js'))); ?>"></script>
<script src="<?php echo e(asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js'))); ?>"></script>
<script src="<?php echo e(asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js'))); ?>"></script>
<script src="<?php echo e(asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js'))); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
$(function() {
    var table = $('.organizations-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?php echo e(route("superadmin.organizations.index")); ?>',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhrFields: {
                withCredentials: true
            },
            dataSrc: function(json) {
                console.log('Organizations data:', json);
                if (json.success && json.data) {
                    return json.data;
                }
                console.error('No data received:', json);
                return [];
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', error, thrown);
                console.error('Response:', xhr.responseText);
                console.error('Status:', xhr.status);
                if (xhr.status === 401) {
                    alert('Session expired. Please refresh the page and login again.');
                }
            }
        },
        columns: [
            { data: 'name' },
            { data: 'email', defaultContent: '-' },
            { 
                data: 'admin',
                render: function(data) {
                    return data ? data.name : '-';
                }
            },
            {
                data: 'registration_source',
                render: function(data) {
                    if (data === 'self_registered') {
                        return '<span class="badge badge-light-primary">Self Registered</span>';
                    }
                    return '<span class="badge badge-light-info">Invited</span>';
                }
            },
            {
                data: 'overall_status',
                render: function(data, type, row) {
                    const statusMap = {
                        'pending': '<span class="badge badge-light-warning">Pending Invitation</span>',
                        'joined': '<span class="badge badge-light-info">Joined (Not Subscribed)</span>',
                        'subscribed': '<span class="badge badge-light-success">Subscribed' + (row.is_on_trial ? ' (Trial)' : '') + '</span>',
                        'expired': '<span class="badge badge-light-danger">Invitation Expired</span>',
                        'registered': '<span class="badge badge-light-primary">Registered</span>'
                    };
                    return statusMap[data] || '<span class="badge badge-light-secondary">-</span>';
                }
            },
            {
                data: 'invitation_status',
                render: function(data, type, row) {
                    // For self-registered organizations, show "N/A"
                    if (row.registration_source === 'self_registered') {
                        return '<span class="badge badge-light-secondary">N/A</span>';
                    }
                    const statusMap = {
                        'none': '<span class="badge badge-light-secondary">No Invitation</span>',
                        'pending': '<span class="badge badge-light-warning">Pending</span>',
                        'joined': '<span class="badge badge-light-info">Joined</span>',
                        'expired': '<span class="badge badge-light-danger">Expired</span>'
                    };
                    return statusMap[data] || '<span class="badge badge-light-secondary">-</span>';
                }
            },
            {
                data: 'is_subscribed',
                render: function(data, type, row) {
                    if (data) {
                        if (row.is_on_trial) {
                            return '<span class="badge badge-light-info">Active (Trial)</span>';
                        }
                        return '<span class="badge badge-light-success">Active</span>';
                    }
                    return '<span class="badge badge-light-warning">Not Subscribed</span>';
                }
            },
            {
                data: 'created_at',
                render: function(data) {
                    if (!data) return '-';
                    const date = new Date(data);
                    return date.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-inline-flex">
                            <a href="/super-admin/organizations/${row.id}" class="btn btn-sm btn-icon" data-toggle="tooltip" title="View">
                                ${feather.icons['eye'].toSvg({ class: 'font-small-4' })}
                            </a>
                            <a href="/super-admin/organizations/${row.id}/edit" class="btn btn-sm btn-icon" data-toggle="tooltip" title="Edit">
                                ${feather.icons['edit'].toSvg({ class: 'font-small-4' })}
                            </a>
                            <form action="/super-admin/organizations/${row.id}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this organization? This will permanently delete all associated data. This action cannot be undone!');">
                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-icon text-danger" data-toggle="tooltip" title="Delete">
                                    ${feather.icons['trash-2'].toSvg({ class: 'font-small-4' })}
                                </button>
                            </form>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']],
        dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-right">><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        language: {
            paginate: {
                previous: "&nbsp;",
                next: "&nbsp;",
            },
            sLengthMenu: "Show _MENU_",
            search: "Search Organizations...",
            searchPlaceholder: "Search Organizations...",
        },
        initComplete: function () {
            $("div.head-label").html('<h5 class="mb-0"><b>Organizations</b></h5>');
            // Initialize tooltips
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/superadmin/organizations/index.blade.php ENDPATH**/ ?>