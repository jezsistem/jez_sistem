














<table class="table table-bordered">
    <thead>
    <tr>
        <th>Size</th>
        <th>Barcode</th>
        <th>Running Code</th>
        <th>Harga Banderol</th>
        <th>Harga Jual</th>
        <th>Harga Beli</th>
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $data['size']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td>
                <label class="checkbox checkbox-outline">
                    <input type="checkbox" data-key="<?php echo e($key+1); ?>" data-id="<?php echo e($row->id); ?>" id="product_size<?php echo e($key+1); ?>" onclick="return getProductSize('<?php echo e($key+1); ?>')"/>
                    <span></span>
                    <?php echo e($row->sz_name); ?> - <?php echo e($row->psc_id); ?>

                </label>
            </td>
            <td>
                <input type="text" class="form-control psb_hidden" id="product_size_barcode<?php echo e($key+1); ?>" data-barcode="<?php echo e($row->id); ?>" data-id="<?php echo e($row->id); ?>" onchange="return getProductSizeBarcode('<?php echo e($key+1); ?>')" placeholder="Barcode" style="display:none;"/>
            </td>
            <td>
                <input type="text" class="form-control psb_hidden" id="product_size_running_code<?php echo e($key+1); ?>" data-running-code="<?php echo e($row->id); ?>" data-id="<?php echo e($row->id); ?>" style="display:none;"/>
            </td>
            <td>
                <input type="number" class="form-control psb_hidden" id="product_size_price_tag<?php echo e($key+1); ?>" data-price-tag="<?php echo e($row->id); ?>" onchange="return getProductPriceTag('<?php echo e($key+1); ?>')" placeholder="Harga Banderol" style="display:none;"/>
            </td>
            <td>
                <input type="number" class="form-control psb_hidden" id="product_size_sell_price<?php echo e($key+1); ?>" data-sell-price="<?php echo e($row->id); ?>" onchange="return getProductSellPrice('<?php echo e($key+1); ?>')" placeholder="Harga Jual" style="display:none;"/>
            </td>
            <td>
                <input type="number" class="form-control psb_hidden" id="product_size_purchase_price<?php echo e($key+1); ?>" data-purchase-price="<?php echo e($row->id); ?>" onchange="return getProductPurchasePrice('<?php echo e($key+1); ?>')" placeholder="Harga Beli" style="display:none;"/>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/product/_reload_size_schema.blade.php ENDPATH**/ ?>