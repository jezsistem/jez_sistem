<div class="modal fade text-left" id="choosecustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title" id="myModalLabel13">Customer</h4>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="f_customer">
                    <input type="hidden" id="_mode" name="_mode"/>
                    <input type="hidden" id="_id" name="_id"/>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Tipe Customer</label>
                            <fieldset class="form-group mb-3">
                                <select class="js-states form-control bg-transparent p-0 border-0" id="ct_id"
                                        name="ct_id" required>
                                    <option value="">- Pilih -</option>
                                    <?php $__currentLoopData = $data['ct_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Nama Customer *</label>
                            <fieldset class="form-group mb-3">
                                <input type="text" id="cust_name" name="cust_name" class="form-control"
                                       placeholder="Nama" required>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">No Telp *</label>
                            <fieldset class="form-group mb-3">
                                <input type="number" id="cust_phone" name="cust_phone" class="form-control"
                                       placeholder="No HP">
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Email</label>
                            <fieldset class="form-group mb-3">
                                <input type="text" id="cust_email" name="cust_email" class="form-control"
                                       placeholder="Email">
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Provinsi</label>
                            <fieldset class="form-group mb-3">
                                <select class="form-control" id="cust_province" name="cust_province">
                                    <option value="">- Pilih -</option>
                                    <?php $__currentLoopData = $data['cust_province']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Kota</label>
                            <fieldset class="form-group mb-3">
                                <select class="form-control" id="cust_city" name="cust_city">
                                    <option value="">- Pilih -</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Kecamatan</label>
                            <fieldset class="form-group mb-3">
                                <select class="form-control" id="cust_subdistrict" name="cust_subdistrict">
                                    <option value="">- Pilih -</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Alamat</label>
                            <fieldset class="form-group mb-3">
                                <input type="text" id="cust_address" name="cust_address" class="form-control"
                                       placeholder="Alamat">
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-body">Status Customer</label>
                            <fieldset class="form-group mb-3">
                                <select class="form-control" id="cust_token_active" name="cust_token_active">
                                    <option value="null">-- Silahkan Pilih --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Non-Active</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group row justify-content-end mb-0">
                        <div class="col-md-6  text-right">
                            <button type="submit" class="btn btn-primary" id="save_customer_btn">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="payment-offline-popup" role="dialog" aria-labelledby="myModalLabel11"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h3 class="modal-title text-white" id="myModalLabel11">Pembayaran</h3>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body bg-white">
                <table class="table right-table">
                    <tbody>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn btn-inventory">Total Bayar</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <h4><span id="payment_total"></span></h4>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="payment_type_content">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn btn-inventory">Down Payment</span>
                        </th>
                        <td class="col-4 border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <input type="checkbox" id="dp_checkbox" value="dp"/>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="payment_type_content">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn btn-inventory">Metode Pembayaran</span>
                        </th>
                        <td class=" col-4 border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <select id="payment_option">
                                <option value="">- Pilih -</option>
                                <option value="one" selected>1 Metode</option>
                                <option value="two">2 Metode</option>
                            </select>
                            <div id="payment_option_parent"></div>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="payment_type_content">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-primary">Jenis Pembayaran</span>
                        </th>
                        <td class=" col-4 border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <select id="pm_id_offline">
                                <option value="">- Pilih -</option>
                                <?php $__currentLoopData = $data['payment_method']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="pm_id_offline_parent"></div>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="card_provider_content">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-primary">Mesin EDC</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <select id="cp_id">
                                <option value="">- Pilih -</option>
                                <?php $__currentLoopData = $data['cp_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="card_number_label">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-primary">No. Kartu</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <input class="bg-light-primary" type="text" placeholder="" id="card_number"/>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="ref_number_label">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-primary">Nama Rekening Pengirim</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <input class="bg-light-primary" type="text" placeholder="" id="ref_number"/>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="charge_label">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-primary">Charge (%)</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <input class="bg-light-primary" type="number" placeholder="" id="charge"/>
                        </td>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <input class="bg-light-primary" type="number" placeholder="" id="charge_total" readonly/>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="payment_type_content_two">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-info">Jenis Pembayaran</span>
                        </th>
                        <td class=" col-4 border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <select id="pm_id_offline_two">
                                <option value="">- Pilih -</option>
                                <?php $__currentLoopData = $data['payment_method']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="pm_id_offline_two_parent"></div>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="card_provider_content_two">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-info">Mesin EDC</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <select id="cp_id_two">
                                <option value="">- Pilih -</option>
                                <?php $__currentLoopData = $data['cp_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="card_number_label_two">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-info">No. Kartu</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <input class="bg-light-primary" type="text" placeholder="" id="card_number_two"/>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between" id="ref_number_label_two">
                        <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <span class="btn-sm btn-info">Nama Rekening Pengirim</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                            <input class="bg-light-primary" type="text" placeholder="" id="ref_number_two"/>
                        </td>
                    </tr>
                    <div id="online_mode">
                        <tr class="d-flex align-items-center justify-content-between">
                            <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                                Kode Unik
                            </th>
                            <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                                <input type="text" placeholder="(isi jika online)" id="unique_code"
                                       class="bg-light-primary"/>
                            </td>
                        </tr>
                        <tr class="d-flex align-items-center justify-content-between">
                            <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                                Charge Lain-Lain (+)
                            </th>
                            <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                                <input type="text" placeholder="(isi jika online)" id="another_cost"
                                       class="bg-light-primary"/>
                            </td>
                        </tr>
                        <tr class="d-flex align-items-center justify-content-between">
                            <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                                Biaya Admin (-)
                            </th>
                            <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                                <input type="text" placeholder="(isi jika online)" id="admin_cost"
                                       class="bg-light-primary"/>
                            </td>
                        </tr>
                        <tr class="d-flex align-items-center justify-content-between">
                            <th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
                                <fieldset class="form-control">
                                    <select name="cr_id" id="cr_id" class="form-control">
                                        <option value="">- Pilih Kurir -</option>
                                        <?php $__currentLoopData = $data['courier']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </fieldset>
                            </th>
                            <td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
                                <input type="number" placeholder="Ongkos Kirim (isi jika online)" id="shipping_cost"
                                       class="bg-light-primary"/>
                            </td>
                        </tr>
                    </div>
                    </tbody>
                </table>
                <div class="form-group row" id="total_payment_label">
                    <div class="col-md-6">
                        <label class="text-body"><span class="btn-sm btn-primary font-size-bold">Jumlah yang dibayar Customer</span></label>
                        <fieldset class="form-group mb-3">
                            <input type="textduagweuiq" name="number" class="form-control bg-light-primary"
                                   id="total_payment"
                                   value="" placeholder="Jumlah">
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <label class="text-body" id="total_payment_two_label"><span
                                    class="btn-sm btn-info font-size-bold">Jumlah yang dibayar Customer</span></label>
                        <fieldset class="form-group mb-3">
                            <input type="text" name="number" class="form-control bg-light-primary"
                                   id="total_payment_two" value="" placeholder="Jumlah">
                        </fieldset>
                    </div>
                </div>
                <div class="form-group row" id="return_payment_label">
                    <div class="col-md-12">
                        <div class="p-3 d-flex justify-content-between border-bottom bg-primary">
                            <h5 class="font-size-bold mb-0 text-white">Kembalian</h5>
                            <h5 class="font-size-bold mb-0 text-white"><span id="return_payment"></span></h5>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <label class="text-body">Catatan (Jika ada)</label>
                        <fieldset class="form-label-group ">
                            <textarea class="form-control fixed-size bg-light-primary" id="note" rows="5"
                                      placeholder=""></textarea>
                        </fieldset>
                    </div>
                </div>
                <div class="form-group row justify-content-end mb-0">
                    <div class="col-md-6  text-right">
                        <a href="#" class="btn btn-primary" id="checkout_btn">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="RefundExchangeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Refund / Penukaran [<span
                            id="refund_retur_invoice_label">INV0000000</span>]</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <input id="refund_retur_pt_id" type="hidden"/>
                <table class="table table-hover table-checkable" id="RefundReturtb">
                    <thead class="bg-light text-dark">
                    <tr>
                        <th class="text-dark">No</th>
                        <th class="text-dark">Artikel</th>
                        <th class="text-dark">Tanggal Trx</th>
                        <th class="text-dark">Qty</th>
                        <th class="text-dark">Price</th>
                        <th class="text-dark">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" data-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="InputCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="f_access">
                <input type="hidden" name="_type" id="_type" value=""/>
                <div class="modal-header btn-primary">
                    <h5 class="modal-title text-dark-header" id="exampleModalLabel">Input Kode</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-10">
                            <label for="exampleTextarea">Scan Barcode Staff Anda</label>
                            <input type="text" class="form-control" id="u_secret_code" name="u_secret_code"
                                   autocomplete="off" autofocus required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold">Lanjut Checkout</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<div class="modal fade" id="OfferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <center>
                    <img src="<?php echo e(asset('upload/image/socks.png')); ?>" style="padding-bottom:30px;"/>
                    <h1 class="font-weight-bolder" style="font-size:22px;">Apakah Customer ingin isi rating ?<br/> Jika
                        Ya silahkan input kode akses anda kemudian pilih Ya</h1>
                    <input id="free_sock_access_code" type="password" autocomplete="off" id=""
                           placeholder="Kode Akses Kasir"/><br/><br/>
                    <a class="btn btn-danger" data-dismiss="modal" style="font-size:15px;"
                       id="free_sock_no_btn">Tidak</a>
                    <a class="btn btn-success" style="font-size:15px;" id="free_sock_btn">Ya</a>
                </center>
            </div>
        </div>
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="ProductBarcodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark-header" id="exampleModalLabel">Lengkapi Barcode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <input type="search" class="form-control  col-6" id="p_search"
                       placeholder="Cari artikel / barcode"/><br/>
                <table class="table table-hover table-checkable" id="Ptb">
                    <thead class="bg-light text-dark-header">
                    <tr>
                        <th class="text-dark-header">No</th>
                        <th class="text-dark-header">Brand</th>
                        <th class="text-dark-header">Artikel</th>
                        <th class="text-dark-header">Warna</th>
                        <th class="text-dark-header">Size</th>
                        <th class="text-dark-header">Barcode</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div class="modal fade text-left" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1444"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h3 class="modal-title text-primary" id="myModalLabel1444">Tambah Voucher</h3>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="f_add_voucher">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="text-body">Voucher</label>
                            <fieldset class="form-group mb-3">
                                <div id="voucher-container">
                                    <div class="input-group mb-3">
                                        <input type="text" name="voucher-list[]" class="form-control"
                                               placeholder="Kode Voucher" value="">
                                        <div class="input-group-append ml-3">
                                            <button class="btn btn-xs btn-outline-secondary add-voucher" type="button">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="form-group row justify-content-end mb-0">
                        <div class="col-md-6  text-right">
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div class="modal fade text-left" id="totalDiscountModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1444"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h3 class="modal-title text-primary" id="myModalLabel1444">Tambah Diskon</h3>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="f_add_total_discount">
                    <div class="form-group col-md-6">
                        <label class="text-body">Tipe Diskon</label>
                        <fieldset class="form-group mb-3">
                            <select class="form-control" name="discount-type-list">
                                <option value="nominal">Nominal</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <label class="text-body">Diskon</label>
                        <fieldset class="form-group mb-3">
                            <div id="total-discount-container">
                                <div class="input-group mb-3">
                                    <input type="text" name="total-discount-list[]" class="form-control"
                                           placeholder="Diskon" value="">
                                    <div class="input-group-append ml-3">
                                        <button class="btn btn-xs btn-outline-secondary add-total-discount"
                                                type="button">+
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="form-group row justify-content-end mb-0">
                        <div class="col-md-6  text-right">
                            <button type="button" class="btn btn-warning" id="total_discount_reset">Reset</button>
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div class="modal fade text-left" id="shiftEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title text-dark" id="myModalLabel13">Shift Employee</h4>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="f_shift_customer">
                    <div class="btn-group" role="group">
                        
                        
                        Shift
                        </button>
                    </div>
                    <br>
                    <br>
                    <div id="shiftStatus">Shift not started</div>
                    <br>


                    <div class="clock"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal -->
<div class="modal fade text-left" id="shiftDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title text-dark" id="myModalLabel13">Shift Employee Detail</h4>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                
                
                

                
                
                <div id="UserShiftModalBody"></div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal -->
<div class="modal fade text-left" id="shiftDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title text-dark" id="myModalLabel13">Input Kas</h4>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                
                
                

                
                
                <input type="text" class="form-control" name="id_shift" id="id_shift">
                <div id="UserShiftModalBody"></div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal -->
<div class="modal fade text-left" id="inputKasModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title text-dark" id="myModalLabel13">Input Kas</h4>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="f_shift_customer">
                    <div class="btn-group" role="group">
                        <div class="toggle-button-cover">
                            <div class="button-cover">
                                <div class="button b2" id="button-12">
                                    <input type="checkbox" class="checkbox"/>
                                    <div class="knobs">
                                        <span></span>
                                    </div>
                                    <div class="layer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <br>
                    <div id="shiftStatus">Shift not started</div>
                    <br>


                    <div class="clock"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal -->
<div class="modal fade text-left" id="InputLabaShift" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h4 class="modal-title text-light" id="myModalLabel13">Detail Information Payment</h4>
                <button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0"
                        data-dismiss="modal" aria-label="Close">
                    <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-3" style="margin: 20px">

                <table class="table table-hover table-checkable" style="width: 100%;" id="current-shift-table">
                    <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Total POS Real Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Static data can be removed if loading data via AJAX -->
                    </tbody>
                </table>

                <form id="f_laba_input">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group mb-1 pb-10">
                                <label for="exampleTextarea">Input Uang Kasir (Cash)</label>
                                <input type="number" style="border: 2px solid #000;" class="form-control" id="laba_shift"
                                       placeholder="Rp. ......." name="laba_shift" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal
                        </button>
                        <button type="submit" class="btn btn-dark font-weight-bold">End Shift</button>
                    </div>
                </form>
            </div>


        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/offline_pos/offline_pos_modal.blade.php ENDPATH**/ ?>