<select class="form-control" id="scan_transfer_invoice" name="scan_transfer_invoice">
    <option value="">PILIH INVOICE</option>
    <?php $__currentLoopData = $data['invoice']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>

<script>
    $('#scan_transfer_invoice').select2({
        width: "100%",
        dropdownParent: $('#ScanTransferModal')
    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/dashboard/helper/_reload_scan_transfer_invoice.blade.php ENDPATH**/ ?>