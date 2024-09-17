<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <title>
            Jersey Zone - Invoice Print
        </title>
        <style type="text/css">
            html {
                font-family: "Verdana";
            }
            .content {
                text-align: center;
                width: 82mm;
                margin-left:1mm;
                font-size: 10px;
            }
            .content .title {
                text-align: center;
                width: 97%;
            }
            .title-left {
                text-align: left;
            }
            .content .head-desc {
                margin-top: 10px;
                display: table;
                width: 95%;
            }
            .content .head-desc > div {
                display: table-cell;
            }
            .content .head-desc .date {
                text-align: left;
            }
            .content .head-desc .user {
                text-align: right;
            }
            .content .nota {
                text-align: center;
                margin-top: 5px;
                margin-bottom: 5px;
            }
            .content .separate {
                width: 95%;
                margin-top: 10px;
                margin-bottom: 15px;
                border-top: 1px dashed #000;
            }
            .content .transaction-table {
                width: 95%;
                font-size: 10px;
            }
            .content .transaction-table .name {
                width: 100px;
            }
            .content .transaction-table .qty {
                padding-left:10px;
                text-align: center;
            }
            .content .transaction-table .sell-price, .content .transaction-table .final-price {
                text-align: right;
                width: 65px;
            }
            .content .transaction-table tr td {
                vertical-align: top;
            }
            .content .transaction-table .price-tr td {
                padding-top: 7px;
                padding-bottom: 7px;
            }
            .content .transaction-table .discount-tr td {
                padding-top: 7px;
                padding-bottom: 7px;
            }
            .content .transaction-table .separate-line {
                height: 1px;
                border-top: 1px dashed #000;
            }
            .content .thanks {
                margin-top: 15px;
                text-align: center;
            }
            .content .azost {
                margin-top:5px;
                text-align: center;
                font-size:10px;
            }

            .text-red {
                color: red;
            }
            @media  print {
                @page    { 
                    width: 82mm;
                    margin: 2mm;
                }
            }

        </style>
    </head>
    <body>
        <?php if(!empty($data['transaction'])): ?>
        <div class="content">
            <center>
            <div class="user"><?php echo e($data['invoice']); ?></div><br/>
            <div id="product_qr"></div>
            </center><br/>
            <div class="separate"></div>
            <div class="title">
                <strong><?php echo e($data['transaction']->st_name); ?></strong><br/>
                <?php echo e($data['transaction']->st_address); ?><br/>
                <?php echo e($data['transaction']->st_phone); ?><br/><br/>
                Jersey Zone<br/>
                www.jez.co.id
            </div>

            <div class="head-desc">
                <div class="date">
                    <?php echo e(date('d-m-Y H:i:s', strtotime($data['transaction']->pos_created))); ?><br/>
                    Pembayaran: <?php echo e($data['transaction']->pm_name); ?>

                </div>
                <div class="user">
                    <?php echo e($data['transaction']->u_name); ?><br/>
                    Tipe: <strong><?php echo e($data['transaction']->dv_name); ?></strong>
                </div>
            </div>

            <div class="nota">
                <?php echo e($data['invoice']); ?>

            </div>
            <div class="separate"></div>

            <div class="transaction">
                <table class="transaction-table" cellspacing="0" cellpadding="0">
                    <?php $discount = 0; $nameset = 0; $subtotal = 0; $total_price = 0;
                        $total_discount = $data['transaction']->pos_total_discount;
                        $total_discount_show= $data['transaction']->pos_total_discount ;
                        $total_marketplace = 0; ?>
                    <?php if(!empty($data['transaction_detail'])): ?>
                        <?php $__currentLoopData = $data['transaction_detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $srow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="margin-bottom:5px;">
                            <td class="name">[<?php echo e($srow->br_name); ?>] <?php echo e($srow->p_name); ?> <?php echo e($srow->p_color); ?> (<?php echo e($srow->sz_name); ?>)</td>
                            <td class="qty"><?php echo e($srow->pos_td_qty); ?>x</td>
                            <?php if($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'TIKTOK' AND $data['transaction']->dv_name != 'WEBSITE'): ?>
                                <td class="sell-price">
                                    <s><?php echo e(number_format($srow->productStock->ps_price_tag)); ?></s>
                                    <?php echo e(round($srow->pos_td_marketplace_price/$srow->pos_td_qty)); ?>

                                    <?php if(!empty($srow->pos_td_discount)): ?>
                                        <br/><?php echo e($srow->pos_td_discount); ?>%
                                    <?php endif; ?>
                                    <?php if(!empty($srow->pos_td_discount_number)): ?>
                                        <br/> <span class="text-red">(<?php echo e($srow->pos_td_discount_number); ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="final-price">
                                    <?php if(!empty($srow->pos_td_discount_number)): ?>
                                        <span><?php echo e(number_format(($srow->pos_td_qty * $srow->pos_td_sell_price) - $srow->pos_td_discount_number)); ?></span>
                                    <?php else: ?>
                                        <?php echo e(number_format($srow->pos_td_qty * $srow->pos_td_sell_price)); ?>

                                    <?php endif; ?>
                                    <?php if(!empty($srow->pos_td_discount)): ?>
                                        <br/>(-<?php echo e(number_format($srow->pos_td_qty * ($srow->pos_td_sell_price/100 * $srow->pos_td_discount))); ?>)
                                    <?php endif; ?>
                                </td>
                            <?php else: ?> 
                                <?php if($data['transaction']->is_website == '1'): ?>
                                <td class="sell-price"><?php echo e(number_format($srow->pos_td_sell_price)); ?>

                                </td>
                                <td class="final-price"><?php echo e(number_format($srow->pos_td_qty * $srow->pos_td_sell_price)); ?></td>
                                <?php else: ?> 
                                <td class="sell-price">
                                    <s><?php echo e(number_format($srow->productStock->ps_price_tag)); ?></s>
                                    <br/>
                                    <?php echo e(number_format($srow->pos_td_sell_price)); ?>

                                    <?php if(!empty($srow->pos_td_discount)): ?>
                                    <br/><?php echo e($srow->pos_td_discount); ?>%
                                    <?php endif; ?>
                                    <?php if(!empty($srow->pos_td_discount_number)): ?>
                                        <br/> <span class="text-red">(<?php echo e($srow->pos_td_discount_number); ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="final-price">
                                    <?php if(!empty($srow->pos_td_discount_number)): ?>
                                        <span><?php echo e(number_format(($srow->pos_td_qty * $srow->pos_td_sell_price) - $srow->pos_td_discount_number)); ?></span>
                                    <?php else: ?>
                                        <?php echo e(number_format($srow->pos_td_qty * $srow->pos_td_sell_price)); ?>

                                    <?php endif; ?>
                                    <?php if(!empty($srow->pos_td_discount)): ?>
                                    <br/>(-<?php echo e(number_format($srow->pos_td_qty * ($srow->pos_td_sell_price/100 * $srow->pos_td_discount))); ?>)
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                        <?php 
                        if ($data['transaction']->is_website == '1') {
                            $subtotal += ($srow->pos_td_qty * $srow->pos_td_sell_price); 
                            $total_price += $srow->pos_td_total_price;
                        } else {
                            $nameset += $srow->pos_td_nameset_price; 
                            $subtotal += ($srow->pos_td_qty * $srow->pos_td_sell_price); 
                            $total_price += $srow->pos_td_total_price; 
//                            $total_discount += $srow->pos_td_qty * ($srow->pos_td_sell_price/100 * $srow->pos_td_discount);
                            $total_discount += $srow->pos_td_discount_number;
                            $total_discount_show += $srow->pos_td_discount_number;
                            $total_discount_show += $srow->pos_td_price_item_discount;
//                            $total_marketplace +=     $srow->pos_td_marketplace_price;
                            $total_marketplace+= ($srow->pos_td_qty * $srow->pos_td_sell_price);;
                        }
                        ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <!-- Discount -->
                    <tr class="discount-tr">
                        <td colspan="4">
                            <div class="separate-line"></div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">SUBTOTAL</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            <?php if($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'TIKTOK' AND $data['transaction']->dv_name != 'WEBSITE'): ?>
                                <?php echo e(number_format($total_marketplace)); ?>

                            <?php else: ?> 
                                <?php echo e(number_format($subtotal)); ?>

                            <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">DISKON</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            <?php if($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'TIKTOK' AND $data['transaction']->dv_name != 'WEBSITE'): ?>
                                    (
                                    <?php if(!empty($total_discount_show)): ?>
                                        <?php echo e(($total_discount_show)); ?>

                                    <?php else: ?>
                                        0
                                    <?php endif; ?>
                                    )
                            <?php else: ?>
                            <span class="text-red">
                            (
                            <?php if(!empty($total_discount_show)): ?>
                            <?php echo e(($total_discount_show)); ?>

                            <?php else: ?> 
                            0
                            <?php endif; ?>
                            )
                                </span>
                            <?php endif; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">ONGKIR</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;"><?php echo e((number_format($data['transaction']->pos_shipping))); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">NAMESET</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;"><?php echo e((number_format($nameset))); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">BIAYA LAIN</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;"><?php echo e((number_format($data['transaction']->pos_another_cost))); ?></span>
                        </td>
                    </tr>
                    <?php if($data['transaction']->is_website == '1'): ?>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">KODE UNIK</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;"><?php echo e((number_format($data['transaction']->pos_unique_code))); ?></span>
                        </td>
                    </tr>     
                    <?php endif; ?>
                    <?php if(!empty($data['transaction']->pos_discount)): ?>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">ADDITIONAL DISCOUNT</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            <?php echo e($data['transaction']->pos_discount); ?> %
                            </span>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left; font-weight:bold;">TOTAL AKHIR</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            <?php if($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'WEBSITE'): ?>
                                <?php 
//                                $totals = $total_marketplace+$nameset+$data['transaction']->pos_another_cost;
                                $totals = $total_marketplace+$nameset+$data['transaction']->pos_another_cost;
                                if (!empty($data['transaction']->pos_discount)) {
                                    $totals = $totals - ($totals/100 * $data['transaction']->pos_discount_number);
                                }
//                                if(!empty($data['transaction']->pos_td_discount_number)) {
//                                    $totals -= $total_discount;
//                                }

                                if ($total_discount > 0) {
                                    $totals -= $total_discount;
                                }

                                ?>
                                <?php echo e(number_format($totals)); ?>

                            <?php else: ?>
                                <?php 
                                if (!empty($total_discount)) {
                                    $totals = $subtotal-$total_discount+$data['transaction']->pos_shipping+$nameset+$data['transaction']->pos_another_cost;
                                } else {
                                    if ($data['transaction']->is_website == '1') {
                                        $totals = $subtotal+$data['transaction']->pos_shipping+$nameset+$data['transaction']->pos_unique_code;
                                    } else {
                                        $totals = $subtotal+$data['transaction']->pos_shipping+$nameset+$data['transaction']->pos_another_cost;
                                    }
                                }
                                if (!empty($data['transaction']->pos_discount)) {
                                    $totals = $totals - ($totals/100 * $data['transaction']->pos_discount);
                                }

                                if(!empty($data['transaction']->pos_discount_number)) {
                                    $totals = $totals - $data['transaction']->pos_discount_number;
                                }
                                ?> 
                                <?php echo e(number_format($totals)); ?>

                            <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left; margin-top:10px;"><i>* Harga sudah termasuk PPN</i></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="thanks">
                ~~~ Terimakasih ~~~
            </div>
            <div class="azost">
                www.jez.co.id
            </div>

            <div class="separate"></div>

            <div class="nota">
                <?php echo e($data['invoice']); ?><br/>
                <?php if($data['transaction']->is_website == '1'): ?>
                <strong><?php echo e($data['transaction']->pos_courier); ?></strong>
                <?php else: ?>
                <strong><?php echo e($data['transaction']->cr_name); ?></strong>
                <?php endif; ?>
            </div>
            <div class="title-left">
                <strong>PENERIMA</strong><br/>
                <?php echo e($data['customer']->cust_name); ?><br/>
                <?php echo e($data['customer']->cust_address); ?>, <?php echo e($data['cust_subdistrict']); ?>, <?php echo e($data['cust_city']); ?>, <?php echo e($data['cust_province']); ?><br/>
                <?php echo e($data['customer']->cust_phone); ?>

            </div><br/>
            <?php if(!empty($data['dropshipper'])): ?>
            <div class="title-left">
                <strong>PENGIRIM</strong><br/>
                <?php echo e($data['dropshipper']->cust_store); ?><br/>
                <?php echo e($data['dropshipper']->cust_phone); ?>

            </div>
            <?php else: ?> 
            <div class="title-left">
                <strong>PENGIRIM</strong><br/>
                <?php echo e($data['transaction']->st_name); ?><br/>
                <?php echo e($data['transaction']->st_phone); ?>

            </div>
            <?php endif; ?>
            <br/>
            <div class="title">
                <strong><i>KOMPLAIN TIDAK KAMI TERIMA TANPA VIDEO<br/>UNBOXING</i></strong>
            </div>
        </div>
        <?php endif; ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js" ></script>
        <script>
            function generateQR()
            {
                var value="<?php echo e($data['invoice']); ?>";
                $('#product_qr').empty();
                $('#product_qr').css({
                    'width' : 100,
                    'height' : 100
                })
                $('#product_qr').qrcode({width: 100,height: 100,text: value});
            }

            $(document).ready(function() {
                generateQR();
                window.print();
            });
        </script>
    </body>
</html><?php /**PATH /home/dev.jez.co.id/public_html/resources/views/app/invoice/print_invoice.blade.php ENDPATH**/ ?>