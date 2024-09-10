<select class="arabic-select-reload-refund select-down" id="refund_invoice" name="refund_invoice">
    <option value="">- Pilih -</option>
    <?php $__currentLoopData = $data['invoice']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<script>
jQuery(function() {
    jQuery('.arabic-select-reload-refund').multipleSelect({
        filter: true,
        filterAcceptOnEnter: true
    })
});
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/offline_pos/_reload_refund.blade.php ENDPATH**/ ?>