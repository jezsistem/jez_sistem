<select class="form-control" id="invoice" name="invoice">
    <option value="">PILIH JIKA SCANNER ERROR</option>
    <?php $__currentLoopData = $data['invoice']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>

<script>
    $('#invoice').select2({
        width: "100%",
        dropdownParent: $('#ScannerModal')
    });

    $('#invoice').on('change', function() {
		var invoice = $(this).val();
		$('#scanned-result').val(invoice).trigger('change');
	});
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/dashboard/helper/_reload_order_invoice.blade.php ENDPATH**/ ?>