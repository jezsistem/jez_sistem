<script>
<?php if($data['label'] == 'sales'): ?>
brChart_render.updateSeries([{
            name: 'Total Omset (Non Admin)',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['sales']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'profits'): ?>
brChart_render.updateSeries([{
            name: 'Total Profit (Non Admin)',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['profits']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'cross_sales'): ?>
brChart_render.updateSeries([{
            name: 'Total Omset (Non Admin)',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['csales']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'cross_profits'): ?>
brChart_render.updateSeries([{
            name: 'Total Profit (Non Admin)',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['profits']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'purchases'): ?>
brChart_render.updateSeries([{
            name: 'Total Pembelian',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['purchases']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'cc_assets'): ?>
brChart_render.updateSeries([{
            name: 'Total Cash/Credit',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['cc_assets']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'c_assets'): ?>
brChart_render.updateSeries([{
            name: 'Total Consignment',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['c_assets']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'cc_exc_assets'): ?>
brChart_render.updateSeries([{
            name: 'Total Cash/Credit',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['cc_assets']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'c_exc_assets'): ?>
brChart_render.updateSeries([{
            name: 'Total Consignment',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['c_assets']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>

<?php if($data['label'] == 'debts'): ?>
brChart_render.updateSeries([{
            name: 'Total Hutang',
            data: [
            <?php $__currentLoopData = $data['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                x: '<?php echo e($row['br_name']); ?>',
                y: [<?php echo e($row['debts']); ?>],
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }]);
<?php endif; ?>
</script>
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/updated_dashboard/_load_graph.blade.php ENDPATH**/ ?>