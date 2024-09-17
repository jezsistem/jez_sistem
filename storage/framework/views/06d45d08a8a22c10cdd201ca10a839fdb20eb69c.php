<select class="form-control" id="cust_city" name="cust_city" required>
    <option value="">- Pilih -</option>
    <?php $__currentLoopData = $data['cust_city']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<div id="cust_city_parent"></div><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/customer/_reload_city.blade.php ENDPATH**/ ?>