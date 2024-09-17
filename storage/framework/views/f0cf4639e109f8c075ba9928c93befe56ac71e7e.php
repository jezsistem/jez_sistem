<select class="form-control mt-2 bg-primary text-white" id="bin_filter">
    <option value='all'>- Semua BIN -</option>
    <?php $__currentLoopData = $data['pl_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<?php /**PATH /home/dev.jez.co.id/public_html/resources/views/app/mass_adjustment/_load_bin.blade.php ENDPATH**/ ?>