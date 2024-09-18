<div class="col-xl-3 col-lg-4 col-md-4 sidebarPOS" style="display:none;">
    <div class="card card-custom gutter-b bg-white border-0">
        <div class="card-body">
            <div class="shop-profile">
                <div class="media">
                    <div class="bg-white w-100px h-100px d-flex justify-content-center align-items-center">
                        
                        <img class="img-fluid" src="<?php echo e(asset('logo')); ?>/LOGOJEZ.png" alt="LOGO JEZ">
                    </div>
                    <div class="media-body ml-3">
                        <?php if($data['shift_status'] > 0): ?>
                            <h3 class="title font-weight-bold"><?php echo e($data['store']->st_name); ?></h3> <h3
                                    class="title font-weight-bold" id="shiftStatus">[Shift In-Progress]</h3>
                        <?php else: ?>
                            <h3 class="title font-weight-bold"><?php echo e($data['store']->st_name); ?></h3> <h3
                                    class="title font-weight-bold" id="shiftStatus">[Shift not started]</h3>
                        <?php endif; ?>
                        <p class="phoonenumber">
                            <?php echo e($data['store']->st_phone); ?>

                        </p>
                        <p class="adddress">
                            <?php echo e($data['store']->st_address); ?>

                        </p>
                    </div>
                </div>
            </div>
            <div class="resulttable-pos">
                <table class="table right-table">
                    <tbody>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-dark">
                            Total Item
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base">
                            <span id="total_item_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-dark">
                            Total Harga
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base">
                            <span id="total_price_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-dark">
                             Total Nameset
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span
                                    id="total_nameset_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0">
                            <div class="d-flex align-items-center font-size-h5 mb-0 font-size-bold text-dark">
                                Voucher new &nbsp;<span id="shipping_courier_side"></span>
                                <input type="hidden" id="shipping_courier_value"/>
                                <span class="badge badge-primary white rounded-circle ml-2" data-toggle="modal"
                                      data-target="#voucherModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-sm"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_11" x="0px"
                                     y="0px" width="512px" height="512px" viewBox="0 0 512 512"
                                     enable-background="new 0 0 512 512" xml:space="preserve">
                                    <g>
                                    <rect x="234.362" y="128" width="43.263" height="256"></rect>
                                    <rect x="128" y="234.375" width="256" height="43.25"></rect>
                                    </g>
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span
                                    id="voucher_total_value_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0">
                            <div class="d-flex align-items-center font-size-h5 mb-0 font-size-bold text-dark">
                                Diskon Total &nbsp;<span id="total_discount_side"></span>
                                <input type="hidden" id="total_discount_value"/>
                                <span class="badge badge-primary white rounded-circle ml-2" data-toggle="modal"
                                      data-target="#totalDiscountModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-sm"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_11" x="0px"
                                     y="0px" width="512px" height="512px" viewBox="0 0 512 512"
                                     enable-background="new 0 0 512 512" xml:space="preserve">
                                    <g>
                                    <rect x="234.362" y="128" width="43.263" height="256"></rect>
                                    <rect x="128" y="234.375" width="256" height="43.25"></rect>
                                    </g>
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span
                                    id="total_discount_value_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between bg-primary rounded">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-white pl-2">
                            Voucher
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base pl-4">
                            <form id="f_voucher">
                                <input class="col-10" id="voucher_code" placeholder="Enter stl input" type="text"/>
                                <a href="#" id="cancel_voucher" class="btn-sm btn-danger ml-2">X</a>
                            </form>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between item-price pl-3 pr-3"
                        style="background:#fef6df;">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-primary">
                            Grand Total
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-base"><span
                                    id="total_final_price_side">0</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>

            </div>
            
            <div class="d-flex justify-content-end align-items-center flex-column buttons-cash">
                <div>
                    <?php if($data['shift_status'] > 0): ?>
                        <button class="btn btn-primary white mb-2" id="payment_btn">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Bayar
                        </button>
                    <?php else: ?>
                        <button disabled class="btn btn-primary white mb-2" id="payment_btn">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Bayar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/offline_pos/offline_pos_sidebar.blade.php ENDPATH**/ ?>