<?php if(!empty($data['product'])): ?>
<div class="table-responsive">
<table class="table table-hover table-checkable" id="PurchaseOrdertb">
    <thead class="bg-light text-dark">
        <tr>
            <th class="text-dark">No</th>
            <th class="text-dark">Artikel</th>
            <th class="text-dark">Stok In (all cabang)</th>
            <th class="text-dark">Diskon</th>
            <th class="text-dark">Detail</th>
            <th class="text-dark">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; $final_price = 0; $a = 0; ?>
        <?php $__currentLoopData = $data['product']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td>
                <?php echo e($no); ?>

            </td>
            <td style="white-space: nowrap;">
            [<?php echo e($row->br_name); ?>] <?php echo e($row->article_id); ?> - <?php echo e($row->p_name); ?><br/><?php echo e($row->p_color); ?> <img onclick="return deletePoa( <?php echo e($row->poa_id); ?>, <?php echo e($row->po_id); ?> )" src="<?php echo e(asset('cdn/details_close.png')); ?>"/><br/>
                <input style="width:155px;" type="month" name="poa_reminder" id="poa_reminder<?php echo e($row->poa_id); ?>" class="form-control" placeholder="Reminder" value="<?php echo e($row->poa_reminder); ?>" onchange="return reminder( <?php echo e($row->poa_id); ?> )"/>

            </td>
            <td>

                <input type="text" style="width:65px;" value="" readonly/>
            </td>
            <td style="white-space: nowrap;">
                <input type="text" style="width:65px;" value="Disc" readonly/><input type="text" name="poa_discount" id="poa_discount<?php echo e($row->poa_id); ?>" style="width:33px;" value="<?php echo e($row->poa_discount); ?>" onchange="return discount( <?php echo e($row->poa_id); ?> )"/><br/>
                <input type="text" style="width:65px;" value="Ex. Disc" readonly/><input type="text" name="poa_extra_discount" id="poa_extra_discount<?php echo e($row->poa_id); ?>" style="width:33px;" value="<?php echo e($row->poa_extra_discount); ?>" onchange="return extraDiscount( <?php echo e($row->poa_id); ?> )"/><br/>
                <input type="text" disabled style="width:65px;" value="Sub. Disc" readonly/><input disabled type="text" name="poa_sub_discount" id="poa_sub_discount<?php echo e($row->poa_id); ?>" style="width:33px;" value="<?php echo e($row->poa_sub_discount); ?>" onchange="return subDiscount( <?php echo e($row->poa_id); ?> )"/>
            </td>
            <td style="white-space: nowrap;">
                    <input type="text" style="width:60px;" value="Sz" readonly/>
                    <input type="text" class="bg-primary text-white" style="width:50px;" value="Order" readonly/>
                    <input type="text" class="bg-primary text-white" style="width:80px;" value="InStock" readonly/>
                    <input type="text" style="width:100px;" value="Harga Band" readonly/>
                    <input type="text" style="width:100px;" value="Harga Beli" readonly/>
                    <input type="text" style="width:100px;" value="Total" readonly/><br/>
                    <?php if(!empty($row->subitem)): ?>
                        <?php $i = 0; $total_poad_price = 0; ?>
                        <?php $__currentLoopData = $row->subitem; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $srow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span data-poa-<?php echo e($row->poa_id); ?>>
                        <input type="text" style="width:60px;" value="<?php echo e($srow->sz_name); ?>" readonly/>
                        <input type="text" id="poad_qty_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" style="width:50px;" value="<?php echo e($srow->poad_qty); ?>" class="order_po_qty order_po_qty_row<?php echo e($a); ?>" onchange="return orderQty( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->poad_id); ?> )" required/>
                        <input type="text" style="width:70px;" value="<?php echo e($srow['total_pls_qty'] ?? ''); ?>" class="order_po_qty order_po_qty_row<?php echo e($a); ?> bg-light" readonly/>
                        <?php if($srow->ps_price_tag == null || $srow->ps_price_tag == 0): ?>
                        <input type="text" style="width:100px;" id="price_tag_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value="<?php echo e(number_format($row->p_price_tag)); ?>" readonly/>
                            <?php if($srow->poad_purchase_price == null || $srow->poad_purchase_price == 0): ?>
                            <input type="text" style="width:100px;" data-poad-id="<?php echo e($srow->poad_id); ?>" onchange="return poPurchasePrice( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->poad_id); ?>, <?php echo e($row->po_id); ?> )" id="poad_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value=""/>
                            <?php else: ?>
                            <input type="text" style="width:100px;" data-poad-id="<?php echo e($srow->poad_id); ?>" onchange="return poPurchasePrice( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->poad_id); ?>, <?php echo e($row->po_id); ?> )" id="poad_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value="<?php echo e($srow->poad_purchase_price); ?>"/>
                            <?php endif; ?>
                        <?php else: ?>
                             <input type="text" style="width:100px;" id="price_tag_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value="<?php echo e(number_format($srow->ps_price_tag)); ?>" readonly/>
                            <?php if($srow->poad_purchase_price == null || $srow->poad_purchase_price == 0): ?>
                            <input type="text" style="width:100px;" data-poad-id="<?php echo e($srow->poad_id); ?>" onchange="return poPurchasePrice( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->poad_id); ?>, <?php echo e($row->po_id); ?> )" id="poad_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value=""/>
                            <?php else: ?>
                            <input type="text" style="width:100px;" data-poad-id="<?php echo e($srow->poad_id); ?>" onchange="return poPurchasePrice( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->poad_id); ?>, <?php echo e($row->po_id); ?> )" id="poad_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value="<?php echo e($srow->poad_purchase_price); ?>"/>
                            <?php endif; ?>
                        <?php endif; ?>
                        <input type="text" style="width:100px;" id="total_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value="<?php echo e(number_format($srow->poad_total_price)); ?>" readonly/> <img onclick="return deletePoad( <?php echo e($srow->poad_id); ?> )" src="<?php echo e(asset('cdn/details_close.png')); ?>"/><br/>
                        <?php $i ++; $total_poad_price += $srow->poad_total_price; $a++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
            </td>
            <td><span id="poad_total_price_<?php echo e($row->poa_id); ?>"><?php echo e(number_format($total_poad_price)); ?></span></td>
        </tr>
        <?php $no++; $final_price += $total_poad_price; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <!-- <td>
                <a class="btn btn-sm btn-primary" id="save_all_po_btn" onclick="return saveAllPo()">Simpan</a>
            </td> -->
            <td colspan="11">
                    <span class="float-right">
                        <a class="btn-sm btn-primary form-control"><center>Rp. <span id="poad_total_price"><?php echo e(number_format($final_price)); ?></span></center></a>
                    </span>
            </td>
        </tr>
    </tbody>
</table>
</div>
<?php endif; ?>
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/purchase_order/_purchase_order_article_detail.blade.php ENDPATH**/ ?>