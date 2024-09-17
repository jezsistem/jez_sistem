<select class="form-control" id="_pssc_id" name="_pssc_id" disabled required>
    <option value="">- Pilih Sub-Sub Kategori -</option>
    <?php $__currentLoopData = $data['pssc_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/product/_reload_pssc.blade.php ENDPATH**/ ?>