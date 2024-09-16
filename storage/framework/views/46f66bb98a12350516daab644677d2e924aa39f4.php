<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!--begin::Content-->
    <header class="bg-primary text-white ">
        <div class="container text-center m-5">
            <h1>Store Traffic</h1>
        </div>
    </header>

    <main class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mt-5">Klik Button Pria atau Wanita untuk menghitung traffic pengunjung secara online</h2>
                <div class="col">
                    <div class="col-md-6 mb-5 mt-5">
                        <button class="btn btn-primary btn-block" id="btnPria">Pria </button>
                    </div>
                    <div class="col-md-6 mb-5">
                        <button class="btn btn-warning btn-block" id="btnWanita">Wanita </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-success btn-block" id="btnAnak">Anak-Anak </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="mt-5">Hari <?php echo e(\Carbon\Carbon::now()->locale('id')->isoFormat('dddd, DD MMMM YYYY')); ?></h3>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <th>Jumlah</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Pria</td>
                        <td id="countmale"><?php echo e($male); ?></td>
                    </tr>
                    <tr>
                        <td>Wanita</td>
                        <td id="countfemale"><?php echo e($female); ?></td>
                    </tr>
                    <tr>
                        <td>Anak-anak</td>
                        <td id="countchild"><?php echo e($child); ?></td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td id="totalCount"><?php echo e($countsTotal); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <!--end::Content-->

    <?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('app.customer.customer_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('app.customer.customer_traffic_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/customer/customer_traffic.blade.php ENDPATH**/ ?>