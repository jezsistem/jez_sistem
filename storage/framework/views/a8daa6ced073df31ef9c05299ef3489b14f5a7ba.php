<select class="form-control col-md-12" id="psc_id">
    <?php $__currentLoopData = $data['psc_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/stock_data/_reload_sub_category.blade.php ENDPATH**/ ?>