<?php $__env->startSection('title', 'Add Expense'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('panels.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add New Expense</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('expenses.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Category <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-6">
                                <label>Expense Date <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <select name="expense_category_id" id="expense_category_id" class="form-control <?php $__errorArgs = ['expense_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">Select Category</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>" <?php echo e(old('expense_category_id') == $category->id ? 'selected' : ''); ?>>
                                            <?php echo e($category->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                
                                <input type="text" name="category_name" id="category_name" class="form-control <?php $__errorArgs = ['category_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="Enter new category name" style="display: none;" value="<?php echo e(old('category_name')); ?>">
                                
                                <?php $__errorArgs = ['expense_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php $__errorArgs = ['category_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                
                                <div class="mt-2">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="category_existing" name="category_type" value="existing" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="category_existing">Select Existing Category</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="category_new" name="category_type" value="new" class="custom-control-input">
                                        <label class="custom-control-label" for="category_new">Create New Category</label>
                                    </div>
                                </div>
                                
                                <small class="text-muted d-block mt-1">If category doesn't exist, select "Create New Category" and enter the category name.</small>
                            </div>

                            <div class="col-md-6">
                                <input type="date" name="expense_date" class="form-control <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('expense_date', date('Y-m-d'))); ?>" required>
                                <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="amount" step="0.01" min="0" 
                                           class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('amount')); ?>" required>
                                </div>
                                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vendor/Payee</label>
                                <input type="text" name="vendor_payee" class="form-control <?php $__errorArgs = ['vendor_payee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('vendor_payee')); ?>" placeholder="Enter vendor or payee name">
                                <?php $__errorArgs = ['vendor_payee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" 
                                  placeholder="Enter expense description"><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label>Receipt/Invoice</label>
                        <input type="file" name="receipt" id="receipt" class="form-control-file <?php $__errorArgs = ['receipt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this)">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                        <?php $__errorArgs = ['receipt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div id="file-error" class="text-danger mt-1" style="display: none;"></div>
                        <div id="file-preview" class="mt-3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Preview</h6>
                                    <div id="preview-content"></div>
                                    <div class="mt-2">
                                        <small class="text-muted" id="file-info"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save Expense</button>
                        <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
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
    
    // Handle category type toggle
    $('input[name="category_type"]').on('change', function() {
        if ($(this).val() === 'new') {
            $('#expense_category_id').hide().prop('required', false);
            $('#category_name').show().prop('required', true).focus();
        } else {
            $('#category_name').hide().prop('required', false).val('');
            $('#expense_category_id').show().prop('required', true);
        }
    });
    
    // Set initial state based on old input
    <?php if(old('category_type') === 'new' || old('category_name')): ?>
        $('#category_new').prop('checked', true).trigger('change');
    <?php endif; ?>
});

// File validation and preview
function handleFileSelect(input) {
    const file = input.files[0];
    const errorDiv = document.getElementById('file-error');
    const previewDiv = document.getElementById('file-preview');
    const previewContent = document.getElementById('preview-content');
    const fileInfo = document.getElementById('file-info');
    
    // Hide previous errors and previews
    errorDiv.style.display = 'none';
    previewDiv.style.display = 'none';
    errorDiv.textContent = '';
    
    if (!file) {
        return;
    }
    
    // Validate file size (10MB = 10 * 1024 * 1024 bytes)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        errorDiv.textContent = 'File size exceeds 10MB limit. Please choose a smaller file.';
        errorDiv.style.display = 'block';
        input.value = ''; // Clear the input
        return;
    }
    
    // Validate file mime type
    const allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedMimes.includes(file.type)) {
        errorDiv.textContent = 'Invalid file type. Only PDF, JPG, and PNG files are allowed.';
        errorDiv.style.display = 'block';
        input.value = ''; // Clear the input
        return;
    }
    
    // Show file info
    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
    fileInfo.textContent = `File: ${file.name} | Size: ${fileSizeMB} MB | Type: ${file.type}`;
    
    // Show preview for images
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewContent.innerHTML = `<img src="${e.target.result}" class="img-fluid" style="max-height: 400px; max-width: 100%;" alt="Receipt preview">`;
            previewDiv.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else if (file.type === 'application/pdf') {
        // For PDF, show a message with file info
        previewContent.innerHTML = `
            <div class="text-center p-3">
                <i data-feather="file-text" style="width: 64px; height: 64px;"></i>
                <p class="mt-2 mb-0">PDF file selected</p>
                <p class="text-muted small">${file.name}</p>
            </div>
        `;
        previewDiv.style.display = 'block';
        if (feather) {
            feather.replace();
        }
    }
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/expenses/create.blade.php ENDPATH**/ ?>