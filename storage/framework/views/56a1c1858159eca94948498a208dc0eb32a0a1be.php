<select class="form-control" id="cross_invoice" name="cross_invoice">
    <option value="">PILIH INVOICE</option>
    <?php $__currentLoopData = $data['invoice']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>

<script>
    $('#cross_invoice').select2({
        width: "100%",
        dropdownParent: $('#TakeCrossOrderModal')
    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/dashboard/helper/_reload_cross_invoice.blade.php ENDPATH**/ ?>