<?php
    $totalPayment = 0;
    $total_tf_bri = 0;
    $total_tf_bca = 0;
    $total_edc_bni = 0;
    $total_edc_bri = 0;
    $total_edc_bca = 0;
?>

<div class="container bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <h2 class="text-xl font-weight-bold mb-4">SHIFT DETAILS</h2>
    <div class="row row-cols-2 gap-4 mb-6">
        <div class="font-weight-medium col">Name</div>
        <div class="col" id><?php echo e($data['name']); ?></div>
        <div class="font-weight-medium col">Access</div>
        <div class="col"><?php echo e($data['st_name']); ?></div>
        <div class="font-weight-medium col">Starting Shift</div>
        <div class="col"><?php echo e($data['start_time']); ?></div>
        <div class="font-weight-medium col">Ending Shift</div>
        <div class="col"><?php echo e($data['end_time']); ?></div>
    </div>
    <h3 class="text-lg font-weight-bold mb-4">ORDER DETAILS</h3>
    <div class="row row-cols-2 gap-4 mb-6">
        <div class="font-weight-medium col">Sold Items</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span><?php echo e($total_sold_items); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-4 h-4" id="userShiftDetailSoldBtn">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Refunded Items</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span><?php echo e($total_refund_items); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-4 h-4" id="userShiftDetailRefundBtn">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </div>
        <hr/>
        <hr/>
    </div>

    
    <?php if($cashMethods != null): ?>
        <h3 class="text-lg font-weight-bold mb-4">Cash</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">Cash</div>
            <div class="col d-flex justify-content-between align-items-center">
                <?php
                    $totalPayment = number_format($cashMethods->total_pos_payment + $cashMethods->total_pos_payment_partials);
                ?>

                <span>Rp. <?php echo e($totalPayment); ?></span>
            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Cash Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($cashMethods->total_pos_payment_refund)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Cash Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($cashMethods->total_pos_payment_expected)); ?></span>

            </div>
            <hr/>
            <hr/>
        </div>
    <?php endif; ?>

    
    <?php if($bcaMethods != null): ?>
        <h3 class="text-lg font-weight-bold mb-4">EDC BCA</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">BCA</div>
            <div class="col d-flex justify-content-between align-items-center">
                <?php
                    $total_edc_bca = number_format($bcaMethods->total_pos_payment + $bcaMethods->total_pos_payment_partials);
                ?>
                <span>Rp. <?php echo e($total_edc_bca); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">EDC Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($bcaMethods->total_pos_payment_refund)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected EDC Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($bcaMethods->total_pos_payment_expected)); ?></span>

            </div>
            <hr/>
            <hr/>
        </div>
    <?php endif; ?>

    
    <?php if($briMethods != null): ?>
        <h3 class="text-lg font-weight-bold mb-4">EDC BRI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                <?php
                    $total_edc_bri = number_format($briMethods->total_pos_payment + $briMethods->total_pos_payment_partials);
                ?>
                <span>Rp. <?php echo e($total_edc_bri); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">EDC Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($briMethods->total_pos_payment_refund)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected EDC Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($briMethods->total_pos_payment_expected)); ?></span>

            </div>
            <hr/>
            <hr/>
        </div>
    <?php endif; ?>

    
    <?php if($bniMethods != null): ?>
        <h3 class="text-lg font-weight-bold mb-4">EDC BNI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                <?php
                    $total_edc_bni = number_format($bniMethods->total_pos_payment + $bniMethods->total_pos_payment_partials);
                ?>
                <span>Rp. <?php echo e($total_edc_bni); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">EDC Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($bniMethods->total_pos_payment_refund)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected EDC Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($bniMethods->total_pos_payment_expected)); ?></span>

            </div>
            <hr/>
            <hr/>
        </div>
    <?php endif; ?>

    
    <?php if($transferBca != null): ?>
        <h3 class="text-lg font-weight-bold mb-4">TRANSFER BCA</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">TRANSFER BCA</div>
            <div class="col d-flex justify-content-between align-items-center">
                <?php
                    $total_tf_bca = number_format($transferBca->total_pos_payment + $transferBca->total_pos_payment_partials);
                ?>
                <span>Rp. <?php echo e($total_tf_bca); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">TRANSFER BCA Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($transferBca->total_pos_payment_refund)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Transfer Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($transferBca->total_pos_payment_expected)); ?></span>

            </div>
            <hr/>
            <hr/>
        </div>
    <?php endif; ?>

    
    <?php if($transferBri != null): ?>
        <h3 class="text-lg font-weight-bold mb-4">TRANSFER BRI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">TRANSFER BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                <?php
                    $total_tf_bri = number_format($transferBri->total_pos_payment + $transferBri->total_pos_payment_partials);
                ?>
                <span>Rp. <?php echo e($total_tf_bri); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">TRANSFER BRI Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($transferBri->total_pos_payment_refund)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Transfer Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($transferBri->total_pos_payment_expected)); ?></span>

            </div>
            <hr/>
            <hr/>
        </div>
    <?php endif; ?>

    

    <?php
        $total_partials = 0;
    ?>

    <?php if($transferBni != null): ?>
        <h3 class="text-lg font-weight-bold mb-4">TRANSFER BNI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">TRANSFER BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($transferBni->total_pos_payment + $transferBni->total_pos_payment_partials)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">TRANSFER BNI Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span><?php echo e(number_format($transferBni->total_pos_payment_refund)); ?></span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Transfer Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. <?php echo e(number_format($transferBni->total_pos_payment_expected)); ?></span>
            </div>
            <hr/>
            <hr/>
        </div>
    <?php endif; ?>



    <?php
        $total_expected = $totalPayment;
    ?>
    <h3 class="text-lg font-weight-bold mb-4">Total</h3>
    <div class="row row-cols-2 gap-4 mb-6">
        <div class="font-weight-med ium col">Total Expected</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. <?php echo e(number_format($total_expected_payment)); ?></span>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Total Actual</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. <?php echo e(number_format($total_actual_payment + $total_payment_two)); ?></span>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Total Actual Ending Cash</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. <?php echo e(number_format($data['laba_shift'])); ?></span>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Difference</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. <?php echo e(number_format($total_expected_payment - $total_actual_payment)); ?></span>
            
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">GAP Cash (Ending Cash - Actual Cash)</div>
        <div class="col d-flex justify-content-between align-items-center">
            
            <span>Rp. <?php echo e(number_format($data['laba_shift'] - $total_expected_payment)); ?></span>
        </div>
        <hr/>
        <hr/>

    </div>
    
    
    
    
    
    
    
    


    
</div><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/report/shift/_shift_detail.blade.php ENDPATH**/ ?>