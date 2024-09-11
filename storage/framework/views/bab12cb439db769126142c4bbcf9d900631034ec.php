<script>
<?php if($data['label'] == 'offline'): ?>
xchart_render.updateSeries([{
        name: 'Total Qty Waiting Offline',
        data: [
        <?php $__currentLoopData = $data['offline_item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            x: '<?php echo e($row->br_name); ?>',
            y: [<?php echo e($row->total); ?>],
        },
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ]
}]);
<?php endif; ?>

<?php if($data['label'] == 'online'): ?>
xchart_render.updateSeries([{
        name: 'Total Qty Waiting Online',
        data: [
        <?php $__currentLoopData = $data['online_item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            x: '<?php echo e($row->br_name); ?>',
            y: [<?php echo e($row->total); ?>],
        },
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ]
}]);
<?php endif; ?>

<?php if($data['label'] == 'instock'): ?>
xchart_render.updateSeries([{
        name: 'Total Qty Instock',
        data: [
        <?php $__currentLoopData = $data['instock_item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            x: '<?php echo e($row->br_name); ?>',
            y: [<?php echo e($row->total); ?>],
        },
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ]
}]);
<?php endif; ?>

<?php if($data['label'] == 'sales_instock'): ?>
xchart_render.updateSeries([{
        name: 'Total Qty Refund/Exchange',
        data: [
        <?php $__currentLoopData = $data['sales_instock_item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            x: '<?php echo e($row->br_name); ?>',
            y: [<?php echo e($row->total); ?>],
        },
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ]
}]);
<?php endif; ?>

<?php if($data['label'] == 'sales'): ?>
xchart_render.updateSeries([{
        name: 'Total Qty Terjual',
        data: [
        <?php $__currentLoopData = $data['sales_item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            x: '<?php echo e($row->br_name); ?>',
            y: [<?php echo e($row->total); ?>],
        },
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ]
}]);
<?php endif; ?>
</script>
<?php /**PATH /home/jezpro.id/public_html/resources/views/app/stock_tracking/_load_graph.blade.php ENDPATH**/ ?>