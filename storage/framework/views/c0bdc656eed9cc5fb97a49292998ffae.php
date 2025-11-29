<?php $__env->startSection('title', 'Inventory Management'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css'))); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Inventory Items</h4>
                <?php if(!auth()->user()->hasRole('general_staff')): ?>
                <div>
                    <?php if($low_stock_count > 0): ?>
                        <a href="<?php echo e(route('inventory.low-stock')); ?>" class="btn btn-warning mr-1">
                            <i data-feather="alert-triangle" class="mr-1"></i> Low Stock (<?php echo e($low_stock_count); ?>)
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('inventory.items.create')); ?>" class="btn btn-primary">
                        <i data-feather="plus" class="mr-1"></i> Add Item
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.inventory' : 'inventory.items.index')); ?>" class="mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Category</label>
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Search items...">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="low_stock" class="form-check-input" id="lowStock" value="1" <?php echo e(request('low_stock') ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="lowStock">Low Stock Only</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-1">Filter</button>
                            <a href="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.inventory' : 'inventory.items.index')); ?>" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Total Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($item->name); ?></strong></td>
                                <td><?php echo e($item->category ?? '-'); ?></td>
                                <td><?php echo e($item->quantity); ?></td>
                                <td><?php echo e($item->unit); ?></td>
                                <td>$<?php echo e(number_format($item->unit_price, 2)); ?></td>
                                <td><strong>$<?php echo e(number_format($item->total_price, 2)); ?></strong></td>
                                <td>
                                    <?php if($item->isLowStock()): ?>
                                        <span class="badge badge-light-warning">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-light-success">In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="<?php echo e(route(auth()->user()->hasRole('general_staff') ? 'view.inventory.items.show' : 'inventory.items.show', $item)); ?>" class="btn btn-sm btn-icon" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <?php if(!auth()->user()->hasRole('general_staff')): ?>
                                        <button type="button" class="btn btn-sm btn-icon text-success" title="Stock In" data-toggle="modal" data-target="#stockModal" onclick="openStockModal(<?php echo e($item->id); ?>, 'in', '<?php echo e($item->name); ?>')">
                                            <i data-feather="arrow-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon text-danger" title="Stock Out" data-toggle="modal" data-target="#stockModal" onclick="openStockModal(<?php echo e($item->id); ?>, 'out', '<?php echo e($item->name); ?>')">
                                            <i data-feather="arrow-up"></i>
                                        </button>
                                        <a href="<?php echo e(route('inventory.items.edit', $item)); ?>" class="btn btn-sm btn-icon" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('inventory.items.destroy', $item)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">No inventory items found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <?php echo e($items->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Transaction Modal -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="stockTransactionForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Log Stock Transaction</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" id="stockTransactionType" value="in" required>
                    <div class="form-group">
                        <label>Item</label>
                        <div class="alert alert-light-primary mb-2">
                            <strong id="stockModalItemName">-</strong>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <div id="transactionTypeDisplay" class="alert alert-info mb-2">
                            <i data-feather="arrow-down" class="mr-1"></i> <strong>Stock In</strong> - Adding inventory
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" min="1" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Purchase order, usage reason, etc.">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <div id="stockInFields">
                        <div class="form-group">
                            <label>Unit Price</label>
                            <input type="number" name="unit_price" step="0.01" min="0" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Vendor</label>
                            <input type="text" name="vendor" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
$(function() {
    if (feather) {
        feather.replace({ width: 14, height: 14 });
    }

    function openStockModal(itemId, type, itemName) {
        const form = $('#stockTransactionForm');
        form.attr('action', '/inventory/items/' + itemId + '/stock');
        $('#stockTransactionType').val(type);
        $('#stockModalItemName').text(itemName);
        updateTransactionTypeDisplay(type);
    }

    function updateTransactionTypeDisplay(type) {
        const displayDiv = $('#transactionTypeDisplay');
        if (type == 'in') {
            displayDiv.removeClass('alert-danger').addClass('alert-success');
            displayDiv.html('<i data-feather="arrow-down" class="mr-1"></i> <strong>Stock In</strong> - Adding inventory to stock');
            $('#stockInFields').show();
            $('.modal-title').html('<i data-feather="arrow-down" class="mr-1 text-success"></i> Log Stock In');
        } else {
            displayDiv.removeClass('alert-success').addClass('alert-danger');
            displayDiv.html('<i data-feather="arrow-up" class="mr-1"></i> <strong>Stock Out</strong> - Removing inventory from stock');
            $('#stockInFields').hide();
            $('.modal-title').html('<i data-feather="arrow-up" class="mr-1 text-danger"></i> Log Stock Out');
        }
        if (feather) {
            feather.replace();
        }
    }

    // Make functions global
    window.openStockModal = openStockModal;
    window.updateTransactionTypeDisplay = updateTransactionTypeDisplay;

    // Set default when modal opens
    $('#stockModal').on('show.bs.modal', function(e) {
        const type = $('#stockTransactionType').val() || 'in';
        updateTransactionTypeDisplay(type);
    });
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/inventory/index.blade.php ENDPATH**/ ?>