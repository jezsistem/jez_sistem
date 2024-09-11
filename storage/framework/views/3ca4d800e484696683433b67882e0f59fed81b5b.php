<select class="form-control" id="_psc_id" name="_psc_id" disabled required>
    <option value="">- Pilih Sub Kategori -</option>
    <?php $__currentLoopData = $data['psc_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/product/_reload_psc.blade.php ENDPATH**/ ?>