<select class="form-control" id="transfer_invoice" name="transfer_invoice">
    <option value="">PILIH INVOICE</option>
    <?php $__currentLoopData = $data['invoice']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>

<script>
    $('#transfer_invoice').select2({
        width: "100%",
        dropdownParent: $('#TransferModal')
    });
</script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/dashboard/helper/_reload_transfer_invoice.blade.php ENDPATH**/ ?>