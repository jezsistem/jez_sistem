<select class="form-control" id="pl_id" name="pl_id" required>
    <option value="">- Pilih BIN -</option>
    <?php $__currentLoopData = $data['pl_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<div id="pl_id_parent"></div><?php /**PATH /home/jezpro.id/public_html/resources/views/app/adjustment/_reload_location.blade.php ENDPATH**/ ?>