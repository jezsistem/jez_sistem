

















<?php if(!empty($data['pos_complaint'])): ?>
    <?php $i = 0; $total = 0; $pt_id = ''; ?>
    <?php $__currentLoopData = $data['pos_complaint']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $pt_id = $row->pt_id ?>
        <?php if(empty($row->ps_sell_price)): ?>
            <tr data-list-item class="bg-danger text-white pos_item_list<?php echo e($row->pst_id); ?> mb-2"
                id="data_plst<?php echo e($row->plst_id); ?>">
                <td style="white-space: nowrap;">[<?php echo e($row->br_name); ?>

                    ] <?php echo e($row->p_name); ?> <?php echo e($row->p_color); ?> <?php echo e($row->sz_name); ?></td>
                <td>-</td>
                <td>-</td>
                <td><input type="text" class="form-control border-dark col-6 basicInput2<?php echo e($row->pst_id); ?>"
                           id="basicInput2" value="-<?php echo e($row->plst_qty); ?>" readonly></td>
                <td><input type="number" class="col-8" value="-<?php echo e($row->pos_td_nameset_price); ?>"
                           id="nameset_price<?php echo e($i+1); ?>" onchange="return namesetPrice<?php echo e('$i+1'); ?>" readonly/></td>
                <td><span id="sell_price_item<?php echo e($i+1); ?>">-<?php echo e(number_format($row->p_sell_price)); ?></span></td>
                <td><span id="subtotal_item<?php echo e($i+1); ?>">-<?php echo e(number_format($row->p_sell_price*$row->plst_qty)); ?></span>
                </td>
                <td>
                    <div class="card-toolbar text-right"><a href="#" class='saveItem' id="saveItem<?php echo e($i+1); ?>"
                                                            onclick="return saveItem(<?php echo e($i+1); ?>, <?php echo e($row->pst_id); ?>, <?php echo e($row->p_sell_price); ?>, <?php echo e($row->plst_id); ?>, <?php echo e($row->pl_id); ?>)"><i
                                    class="fa fa-eye" style="display:none;"></i></a></div>
                </td>
            </tr>
            <?php $i ++; $total += $row->p_sell_price ?>
        <?php else: ?>
            <tr data-list-item class="bg-danger text-white pos_item_list<?php echo e($row->pst_id); ?> mb-2"
                id="data_plst<?php echo e($row->plst_id); ?>">
                <td style="white-space: nowrap;">[<?php echo e($row->br_name); ?>

                    ] <?php echo e($row->p_name); ?> <?php echo e($row->p_color); ?> <?php echo e($row->sz_name); ?></td>
                <td>-</td>
                <td>-</td>
                <td><input type="text" class="form-control border-dark col-6 basicInput2<?php echo e($row->pst_id); ?>"
                           id="basicInput2" value="-<?php echo e($row->plst_qty); ?>" readonly></td>
                <td><input type="number" class="col-8" value="-<?php echo e($row->pos_td_nameset_price); ?>"
                           id="nameset_price<?php echo e($i+1); ?>" onchange="return namesetPrice<?php echo e('$i+1'); ?>" readonly/></td>
                <td><span id="sell_price_item<?php echo e($i+1); ?>">-<?php echo e(number_format($row->ps_sell_price)); ?></span></td>
                <td><span id="subtotal_item<?php echo e($i+1); ?>">-<?php echo e(number_format($row->ps_sell_price*$row->plst_qty)); ?></span>
                </td>
                <td>
                    <div class="card-toolbar text-right"><a href="#" class='saveItem' id="saveItem<?php echo e($i+1); ?>"
                                                            onclick="return saveItem(<?php echo e($i+1); ?>, <?php echo e($row->pst_id); ?>, <?php echo e($row->ps_sell_price); ?>, <?php echo e($row->plst_id); ?>, <?php echo e($row->pl_id); ?>)"><i
                                    class="fa fa-eye" style="display:none;"></i></a></div>
                </td>
            </tr>
            <?php $i ++; $total += $row->ps_sell_price ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <script>
        <?php if(!empty($pt_id)): ?>
        jQuery('#_pt_id_complaint').val('<?php echo e($pt_id); ?>');
        jQuery('#total_item_side').text('-<?php echo e($i); ?>');
        jQuery('#total_price_side').text('-<?php echo e(number_format($total)); ?>');
        jQuery('#total_final_price_side').text('-<?php echo e(number_format($total)); ?>');
        <?php endif; ?>
    </script>
<?php endif; ?><?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/offline_pos/_reload_complaint.blade.php ENDPATH**/ ?>