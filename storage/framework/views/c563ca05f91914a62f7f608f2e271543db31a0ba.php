<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Product Name</th>
        <th scope="col">Size</th>
        <th scope="col">Qty</th>
    </tr>
    </thead>
    <tbody>

    <?php if($items != null): ?>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <th scope="row"><?php echo e($loop->iteration); ?></th>
                <td><?php echo e($item->product_name); ?></td>
                <td><?php echo e($item->size_name); ?></td>
                <td><?php echo e($item->qty); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php else: ?>
        <tr>
            <td colspan="3" class="text-center">No Refund Items</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/report/shift/_shift_product_sold.blade.php ENDPATH**/ ?>