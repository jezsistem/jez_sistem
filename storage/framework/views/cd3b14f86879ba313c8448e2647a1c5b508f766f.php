<script>
    $('#nett_sales_label_reload').text(addCommas(<?php echo e($data['total']); ?>));
    nsChart_render.updateSeries([
    <?php if(!empty($data['item'])): ?>
    <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    {
        name: '<?php echo e($row['st_name']); ?>',
        data: [<?php echo e($row['total']); ?>],
        color: primary
    },
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    ]);
</script>
<?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/dashboard/_load_sales.blade.php ENDPATH**/ ?>