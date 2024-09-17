<select class="form-control" id="pl_id_end" name="pl_id_end" required>
    <option value="">- BIN Tujuan -</option>
    <?php $__currentLoopData = $data['pl_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<div id="pl_id_end_parent"></div>
<script>
    $('#pl_id_end').select2({
        width: "100%",
        dropdownParent: $('#pl_id_end_parent')
    });
    $('#pl_id_end').on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });
    
    $(document).delegate('#pl_id_end', 'change', function() {
        end_bin_table.draw();
    });
</script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/product_location_setup_v2/_end.blade.php ENDPATH**/ ?>