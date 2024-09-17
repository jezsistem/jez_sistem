<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<?php echo $__env->make('app._partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!--end::Head-->
<?php if(request()->segment(1) == 'rating_app'): ?>
<body id="device_background_panel">
<?php else: ?> 
<body class="d-none" id="device_background_panel">
<?php endif; ?>
    <div class="d-flex flex-column-fluid flex-center" id="device_background">
        <form id="f_rating">
            <?php echo csrf_field(); ?>
            <input type="hidden" value="" id="rating_mode"/>
            <input type="hidden" value="" id="ur_id"/>
            <input type="hidden" value="" id="cust_id"/>
            <center>
            <div style="width:100%;">
                <h3 class="font-weight-bolder text-white font-size-h1-lg" style="font-size:22px;"><?php echo e($data['store_name']); ?></h3><hr>
                <p class="font-weight-bolder text-white">Silahkan beri penilaian Anda tentang produk dan pelayanan kami.</p>

                <h3 class="font-weight-bolder text-white font-size-h1-lg">Terimakasih telah berbelanja</h3>
                <p class="font-weight-bolder text-white">Kasir tidak mengetahui rating yang Anda berikan. </br>Silahkan beri rating <span style="color:#F2C94C;">paling jujur</span>, ya!</p>
                <div class="rating-css">
                    <div class="star-icon">
                    <input type="radio" name="rating1" id="rating1">
                    <label for="rating1" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating2">
                    <label for="rating2" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating3">
                    <label for="rating3" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating4">
                    <label for="rating4" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating5">
                    <label for="rating5" class="fa fa-star"></label>
                    </div>
                </div>
                <input type="hidden" id="rating_value" value="" />

                <h3 class="font-weight-bolder text-white font-size-h1-lg">" <span id="rating_label">Pilih Bintang</span> "</h3>

            </div>
            <div style="width:80%;">

                <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-white float-left">Kritik & Saran</label>
                    <textarea class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="ur_description" id="ur_description" autocomplete="off" placeholder="Tulis alasan"></textarea>

                </div>
                <button type="submit" id="kt_login_signin_submit" class="btn btn-warning font-weight-bolder font-size-h3 px-8 py-4 my-3 mr-2 col-12" style="background-color:#F2C94C;">Kirim</button>
            </div>
            </center>
        </form>
    </div>
    <?php echo $__env->make('app.rating_by_customer.rating_by_customer_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('app.rating_by_customer.rating_by_customer_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/rating_by_customer/rating_by_customer.blade.php ENDPATH**/ ?>