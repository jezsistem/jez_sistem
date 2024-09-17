<!-- <input type="hidden" id="_pc_id" value="" /> -->
<select class="form-control" id="pl_id_start" name="pl_id_start" required>
    <option value="">- BIN Awal -</option>
    <?php $__currentLoopData = $data['pl_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<div id="pl_id_start_parent"></div>

<script>
    $('#pl_id_start').select2({
        width: "100%",
        dropdownParent: $('#pl_id_start_parent')
    });
    $('#pl_id_start').on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });

    $(document).delegate('#pl_id_start', 'change', function() {
        start_bin_table.draw();
    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/product_location_setup_v2/_start.blade.php ENDPATH**/ ?>