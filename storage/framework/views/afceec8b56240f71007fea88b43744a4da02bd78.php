<?php if(!empty($data['product'])): ?>
    <div class="table-responsive">
        <table class="table table-hover table-checkable" id="PurchaseOrdertb">
            <thead class="bg-light text-dark">
                <tr>
                    <th class="text-dark">No</th>
                    <th style="white-space: nowrap;" class="text-light">Artikel</th>
                    <th class="text-dark">Diskon</th>
                    <th class="text-dark">Detail</th>
                    <th style="white-space: nowrap;" class="text-light">Value PO</th>
                    <th style="white-space: nowrap;" class="text-light">Value Terima</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $no = 1;
                    $final_price = 0;
                    $final_price_poads = 0;
                    $a = 0;
                ?>
                <?php $__currentLoopData = $data['product']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <?php echo e($no); ?>

                        </td>
                        <td style="white-space: nowrap;">[<?php echo e($row->br_name); ?>]<br /><?php echo e($row->articleid); ?> -
                            <?php echo e($row->p_name); ?>

                            <br /><?php echo e($row->p_color); ?><br />
                            <?php if($row->poa_reminder != '' || $row->poa_reminder != 0000 - 00 - 00): ?>
                                <?php echo e(date('M Y', strtotime($row->poa_reminder))); ?>

                            <?php endif; ?>
                        </td>
                        <td style="white-space: nowrap;">
                            <input type="text" style="width:65px;" value="Disc" readonly /><input disabled
                                type="text" name="poa_discount" id="poa_discount<?php echo e($row->poa_id); ?>"
                                style="width:33px;" value="<?php echo e($row->poa_discount); ?>"
                                onchange="return discount( <?php echo e($row->poa_id); ?> )" /><br />
                            <input type="text" style="width:65px;" value="Ex. Disc" readonly /><input disabled
                                type="text" name="poa_extra_discount" id="poa_extra_discount<?php echo e($row->poa_id); ?>"
                                style="width:33px;" value="<?php echo e($row->poa_extra_discount); ?>"
                                onchange="return extraDiscount( <?php echo e($row->poa_id); ?> )" /><br />
                            <input type="text" disabled style="width:65px;" value="Sub. Disc" readonly /><input
                                disabled type="text" name="poa_sub_discount"
                                id="poa_sub_discount<?php echo e($row->poa_id); ?>" style="width:33px;"
                                value="<?php echo e($row->poa_sub_discount); ?>"
                                onchange="return subDiscount( <?php echo e($row->poa_id); ?> )" />
                        </td>
                        <td style="white-space: nowrap;">

                            <input type="text" style="width:60px;" value="Sz" readonly />
                            <input type="text" style="width:60px;" value="Ord" readonly />
                            <input type="text" style="width:60px;" value="Krg" readonly />
                            <input type="text" class="bg-primary text-white" style="width:52px;" value="Terima"
                                readonly />
                            <input type="text" class="bg-primary text-white" style="width:62px;" value="InStock"
                                readonly />
                            <input type="text" style="width:80px;" value="Harga Bnd" readonly />
                            <input type="text" style="width:80px;" value="Harga Beli" readonly />
                            <input type="text" style="width:80px;" value="COGS" readonly />
                            <input type="text" style="width:80px;" value="Total" readonly />
                            <input type="text" style="width:90px;" value="Total Terima" readonly />
                            <br />
                            <?php if(!empty($row->subitem)): ?>
                                <?php
                                    $i = 0;
                                    $total_poad_price = 0;
                                    $total_poads_price = 0;
                                ?>
                                <?php $__currentLoopData = $row->subitem; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $srow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span data-poa-<?php echo e($row->poa_id); ?>>
                                        <input type="text" style="width:60px;" value="<?php echo e($srow->sz_name); ?>"
                                            readonly />
                                        <input type="number" style="width:60px;" value="<?php echo e($srow->poad_qty); ?>"
                                            readonly />
                                        <input type="number"
                                            id="poads_qty_remain_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                            style="width:60px;" value="<?php echo e($srow->poad_qty - $srow->poads_qty); ?>"
                                            readonly />
                                        <?php if($srow->poad_qty - $srow->poads_qty == 0): ?>
                                            <input type="text" style="width:52px;" value="Full" disabled />
                                        <?php elseif($srow->qty_import != null): ?>
                                            <input type="text"
                                                id="poads_qty_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                style="width:52px;" value="<?php echo e($srow->qty_import); ?>"
                                                onchange="return receiveQtyImport(<?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->qty_import); ?> )"
                                                required />
                                        <?php else: ?>
                                            <input type="text"
                                                id="poads_qty_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                style="width:52px;" value=""
                                                onchange="return receiveQty(<?php echo e($row->poa_id); ?>, <?php echo e($i); ?> )"
                                                required />
                                        <?php endif; ?>
                                        <input type="text" disabled style="width:65px;"
                                            value="<?php echo e($srow['total_pls_qty'] ?? ''); ?>" readonly />
                                        <?php if($srow->ps_price_tag == null || $srow->ps_price_tag == 0): ?>
                                            <input type="text" style="width:80px;"
                                                id="price_tag_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                value="<?php echo e(number_format($row->p_price_tag)); ?>" readonly />
                                        <?php else: ?>
                                            <input type="text" style="width:80px;"
                                                id="price_tag_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                value="<?php echo e(number_format($srow->ps_price_tag)); ?>" readonly />
                                        <?php endif; ?>
                                        <?php if($srow->qty_import != null || $srow->qty_import != 0): ?>
                                            <?php if($srow->ps_price_tag == null || $srow->ps_price_tag == 0): ?>
                                                <input type="text" style="width:80px;"
                                                    data-poad-id="<?php echo e($srow->poad_id); ?>"
                                                    id="poad_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                    onchange="return receivePurchasePrice( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?> )"
                                                    value="<?php echo e(number_format($srow->qty_import * $row->p_price_tag)); ?>"
                                                    disabled />
                                            <?php else: ?>
                                                <input type="text" style="width:80px;"
                                                    data-poad-id="<?php echo e($srow->poad_id); ?>"
                                                    id="poad_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                    onchange="return receivePurchasePrice( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?> )"
                                                    value="<?php echo e(number_format($srow->qty_import * $srow->ps_price_tag)); ?>"
                                                    disabled />
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <input type="text" style="width:80px;"
                                                data-poad-id="<?php echo e($srow->poad_id); ?>"
                                                id="poad_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                onchange="return receivePurchasePrice( <?php echo e($row->poa_id); ?>, <?php echo e($i); ?> )"
                                                value="<?php echo e($srow->poad_purchase_price ?? 0); ?>" disabled />
                                        <?php endif; ?>
                                        
                                        <input type="text" style="width:80px;"
                                            id="cogs_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>" value=""
                                            readonly />
                                        <input type="text" style="width:80px;"
                                            id="total_purchase_price_<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                            value="<?php echo e(number_format($srow->poad_total_price)); ?>" readonly />
                                        <?php if($srow->qty_import != null): ?>
                                            <input type="text" style="width:90px;"
                                                id="total_purchase_price_receive<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                value="<?php echo e(number_format($srow->qty_import * $srow->poad_total_price)); ?>"
                                                readonly /> <img data-img-poads id="savePoads<?php echo e($a); ?>"
                                                onclick="return savePoads( <?php echo e($srow->poad_id); ?>, <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->pst_id); ?> )"
                                                src="<?php echo e(asset('cdn/details_open.png')); ?>"
                                                ondblclick="return bulkSavePoads( <?php echo e($srow->poad_id); ?>, <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->pst_id); ?> )"
                                                src="<?php echo e(asset('cdn/details_open.png')); ?>" />
                                        <?php else: ?>
                                            <input type="text" style="width:90px;"
                                                id="total_purchase_price_receive<?php echo e($row->poa_id); ?>_<?php echo e($i); ?>"
                                                value="" readonly /> <img data-img-poads
                                                id="savePoads<?php echo e($a); ?>"
                                                onclick="return savePoads( <?php echo e($srow->poad_id); ?>, <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->pst_id); ?> )"
                                                src="<?php echo e(asset('cdn/details_open.png')); ?>"
                                                ondblclick="return bulkSavePoads( <?php echo e($srow->poad_id); ?>, <?php echo e($row->poa_id); ?>, <?php echo e($i); ?>, <?php echo e($srow->pst_id); ?> )"
                                                src="<?php echo e(asset('cdn/details_open.png')); ?>" />
                                        <?php endif; ?>
                                        <i class="fa fa-eye"
                                            data-p_name="[<?php echo e($row->br_name); ?>] <?php echo e($row->p_name); ?> <?php echo e($row->p_color); ?> [<?php echo e($srow->sz_name); ?>]"
                                            data-poad_id="<?php echo e($srow->poad_id); ?>" id="receive_history"> </i>
                                        <br />
                                        <?php
                                            $i++;
                                            $a++;
                                            $total_poad_price += $srow->poad_total_price;
                                            $total_poads_price += $srow->poads_total_price;
                                        ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </td>
                        <td><span
                                id="poad_total_price_<?php echo e($row->poa_id); ?>"><?php echo e(number_format($total_poad_price)); ?></span>
                        </td>
                        <td><span
                                id="poads_total_price_<?php echo e($row->poa_id); ?>"><?php echo e(number_format($total_poads_price)); ?></span>
                        </td>
                    </tr>
                    <?php
                        $no++;
                        $final_price += $total_poad_price;
                        $final_price_poads += $total_poads_price;
                    ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                        <a class="btn btn-sm btn-success float-right" id="save_all_ro_btn"
                            onclick="return saveAllPoads()">Terima</a>
                    </td>
                    <td>
                        <span class="float-right">
                            <input type="text" style="width:90px;" id="poads_total_price_receive"
                                value="<?php echo e(number_format($final_price)); ?>" readonly />
                        </span>
                    </td>
                    <td>
                        <span class="float-right">
                            <input type="text" style="width:90px;" id="poads_total_price_receive"
                                value="<?php echo e(number_format($final_price_poads)); ?>" readonly />
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php /**PATH /home/jezpro.id/public_html/resources/views/app/purchase_order_receive/_purchase_order_article_detail.blade.php ENDPATH**/ ?>