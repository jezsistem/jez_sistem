

<div class="table-responsive">
    <table class="table table-hover table-checkable" id="ProductLocationtb">
        <thead class="bg-light text-dark">
            <tr>
                <th class="text-dark">No</th>
                <th style="white-space: nowrap;" class="text-light">Artikel</th>
                <th style="white-space: nowrap;" class="text-light">Warna</th>
                <th style="white-space: nowrap;" class="text-light">Size [Jumlah] [Barcode]</th>
                <th style="white-space: nowrap;" class="text-light"></th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($data['product'])): ?>
            <?php $__currentLoopData = $data['product']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
            $i=1; 
            $exp = explode('|', $row->p_image);
            ?>

            <tr>
                <td style="white-space: nowrap;"><?php echo e($i); ?></td>
                <td style="white-space: nowrap;"><?php echo e($row->p_name); ?></td>
                <td style="white-space: nowrap;">(<?php echo e($row->mc_name); ?>) <?php echo e($row->p_color); ?></td>
                <td style="white-space: nowrap;">
                    <a class="btn btn-sm btn-primary"><?php echo e($row->sz_name); ?></a> <a class="btn btn-sm btn-primary"><?php echo e($row->pls_qty); ?></a> <a class="btn btn-sm btn-primary"><?php echo e($row->ps_barcode); ?></a>
                </td>
                <td style="white-space: nowrap;">
                    <input type="checkbox" id="check_item_<?php echo e($row->pls_id); ?>" onclick="return checkItem( <?php echo e($row->pls_id); ?> )"/> 
                    <span id="article_controller_<?php echo e($row->pls_id); ?>" style="display:none;">
                    <input type="number" style="width:50px;" placeholder="Qty"/>
                    <select style="width:120px;" id="pl_id" name="pl_id" required>
                        <option value="">- BIN Tujuan -</option>
                        <?php $__currentLoopData = $data['pl_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <a onclick="return mutationArticle()" class="btn btn-sm btn-primary">Mutasi</a>
                    <span>
                </td>
            </tr>
            <?php $i++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </tbody>
    </table>
</div><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/product_location_setup/_product_location_setup_detail.blade.php ENDPATH**/ ?>