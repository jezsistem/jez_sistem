<select class="form-control" id="gr_id" name="gr_id">
    <option value="">- Pilih -</option>
    <?php $__currentLoopData = $data['gr_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/user/_reload_group.blade.php ENDPATH**/ ?>