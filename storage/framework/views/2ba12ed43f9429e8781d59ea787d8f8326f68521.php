<?php if(!empty($data['pos_data'])): ?>
    <?php
        $i = 0;
        $total = 0;
    ?>
    <?php $__currentLoopData = $data['pos_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            if (!empty($row->ps_sell_price)) {
                $sell_price = $row->ps_sell_price;
            } else {
                $sell_price = $row->p_sell_price;
            }

            if (date('Y-m-d') <= $row->pd_date) {
                if (empty($row->st_id)) {
                    if ($row->pd_type == 'percent') {
                        $sell_price = $sell_price - ($sell_price / 100) * $row->pd_value;
                    } elseif ($row->pd_type == 'amount') {
                        $sell_price = $sell_price - $row->pd_value;
                    } else {
                        $sell_price = $sell_price;
                    }
                } else {
                    if (Auth::user()->st_id == $row->st_id) {
                        if ($row->pd_type == 'percent') {
                            $sell_price = $sell_price - ($sell_price / 100) * $row->pd_value;
                        } elseif ($row->pd_type == 'amount') {
                            $sell_price = $sell_price - $row->pd_value;
                        } else {
                            $sell_price = $sell_price;
                        }
                    }
                }
            }

            if ($row->pl_code == 'TOKO' || $row->pl_code == 'TOKA') {
                $status = '';
                $pls_qty = $row->pls_qty + 1;
            } else {
                $status = 'readonly';
                $pls_qty = $row->pls_qty;
            } 
        ?>
        <tr data-list-item class='pos_item_list"<?php echo e($row->pst_id); ?>" mb-2 bg-light-primary'
            id='orderList<?php echo e($i + 1); ?>'>
            <td style='white-space: nowrap; font-size:14px;' id='item_name<?php echo e($i + 1); ?>'>[<?php echo e($row->br_name); ?>]
                <?php echo e($row->p_name); ?> <?php echo e($row->p_color); ?> <?php echo e($row->sz_name); ?>

            </td>
            <td><?php echo e($pls_qty); ?></td>
            <td><input type='number' class='form-control border-dark col-4 basicInput2<?php echo e($row->pst_id); ?>'
                    id='item_qty<?php echo e($i + 1); ?>' value='1'
                    onchange='return changeQty(<?php echo e($i + 1); ?>, <?php echo e($row->pst_id); ?>, <?php echo e($pls_qty); ?>)'
                    <?php echo e($status); ?>>            
            </td>
            <td>
                <input type="text" class="form-control border-dark col-5 basicInput2<?php echo e($row->pst_id); ?>"
                id="discount_percentage<?php echo e($i+1); ?>" value="0" onchange="return changeDiscountPercentage(<?php echo e($i+1); ?>, <?php echo e($row->pst_id); ?>, <?php echo e($pls_qty); ?>)",/>
            </td>
            <td>
                <input type="text" class="form-control border-dark col-5 basicInput2<?php echo e($row->pst_id); ?>"
                id="discount_number<?php echo e($i+1); ?>" value="0" onchange="return changeDiscountNumber(<?php echo e($i+1); ?>, <?php echo e($row->pst_id); ?>, <?php echo e($pls_qty); ?>)",/>
            </td>
            <td><input type='number' class='col-8 nameset_price' id='nameset_price<?php echo e($i + 1); ?>'
                    onchange='return namesetPrice(<?php echo e($i + 1); ?>)' /></td>
            <td><span id='sell_price_item<?php echo e($i + 1); ?>'><?php echo e(number_format($sell_price)); ?></span></td>
            <td><span class='subtotal_item'
                    id='subtotal_item<?php echo e($i + 1); ?>'><?php echo e(number_format($sell_price)); ?></span></td>
            <td>
                <div class='card-toolbar text-right'><a href='#' class='saveItem'
                        id='saveItem<?php echo e($i + 1); ?>'
                        onclick='return saveItem(<?php echo e($i + 1); ?>, <?php echo e($row->pst_id); ?>, <?php echo e($row->p_sell_price); ?>, <?php echo e($row->plst_id); ?>, <?php echo e($row->pl_id); ?>)'><i
                            class='fa fa-eye' style='display:none;'></i></a>
                    <a href='#' class='confirm-delete' title='Delete'
                        onclick='return deleteItem(<?php echo e($row->pst_id); ?>, <?php echo e($sell_price); ?>, <?php echo e($i + 1); ?>, <?php echo e($row->pl_id); ?>, <?php echo e($row->plst_id); ?>)'><i
                            class='fas fa-trash-alt'></i></a>
                </div>
            </td>
        </tr>
        <?php
            $i++;
            $total += $sell_price;
        ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<script>
    jQuery('#total_item_side').text('<?php echo e($i); ?>');
    jQuery('#total_price_side').text('<?php echo e(number_format($total)); ?>');
    jQuery('#total_final_price_side').text('<?php echo e(number_format($total)); ?>');
</script>
<?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/offline_pos/_reload_waiting_for_checkout.blade.php ENDPATH**/ ?>