<!-- Modal-->
<div class="modal fade" id="PurchaseOrderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1300px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Penerimaan PO #<span
                        id="po_invoice_label"></span></h5>
                <!--begin::Button Import-->
                <div class="mr-2">
                    <div class="dropdown dropdown-inline mr-2">
                        <a type="button" class="btn btn-light-primary font-weight-bolder" id="ImportModalBtn"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>Import</a>
                    </div>
                    <!--begin::Dropdown Menu-->
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi flex-column navi-hover py-2">
                            <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                Bentuk File :
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-copy"></i>
                                    </span>
                                    <span id="purchase_order_excel_btn"></span>
                                </a>
                            </li>
                        </ul>
                        <!--end::Navigation-->
                    </div>
                    <!--end::Dropdown Menu-->
                </div>
                <!--end::Dropdown-->
            </div>
            <form id="f_po" enctype="multipart/form-data">
                <input type="hidden" id="_mode" name="_mode" />
                <input type="hidden" id="_po_id" name="_po_id" />
                <div class="modal-body">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-4">
                            <label>Store</label>
                            <select class="form-control" id="st_id" name="st_id" required disabled>
                                <option value="">- Pilih Store -</option>
                                <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="st_id_parent"></div>
                        </div>
                        <div class="col-4">
                            <label>Supplier</label>
                            <select class="form-control" id="ps_id" name="ps_id" required disabled>
                                <option value="">- Pilih Supplier -</option>
                                <?php $__currentLoopData = $data['ps_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="ps_id_parent"></div>
                        </div>
                        <div class="col-4">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="po_description" id="po_description" rows="3"></textarea>
                        </div>
                        <div class="col-4">
                            <label>Tipe Stok * otomatis dari master PO jika diisi oleh tim terkait</label>
                            <select class="form-control" id="stkt_id" name="stkt_id" required disabled>
                                <option value="">- Pilih Tipe Stok -</option>
                                <?php $__currentLoopData = $data['stkt_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="stkt_id_parent"></div>
                        </div>
                        <div class="col-4">
                            <label>Pajak</label>
                            <select class="form-control" id="tax_id" name="tax_id" required disabled>
                                <option value="">- Pajak -</option>
                                <?php $__currentLoopData = $data['tax_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="tax_id_parent"></div>
                        </div>
                        <div class="col-4">
                            <label>Tanggal Terima</label>
                            <input type="date" id="receive_date" class="form-control" value="" />
                        </div>
                        <div class="col-4 mt-4">
                            <label class="badge badge-primary">Invoice</label>
                            <input type="text" id="receive_invoice" class="form-control" value=""
                                placeholder="INVxx" />
                        </div>
                        <div class="col-4 mt-4">
                            <label class="badge badge-primary">Tanggal Invoice</label>
                            <input type="date" id="invoice_date" class="form-control" value="" />
                        </div>
                        <div class="col-4 mt-4 d-flex flex-column">
                            <label class="badge badge-primary">Bukti Gambar Invoice dan Paket</label>
                            <div class="row  justify-content-between">
                                <a class="input-group col-5" type="button" id="InvoiceImagesBtn"
                                    aria-haspopup="true" aria-expanded="false">
                                    <label class="input-group-text" for="invoiceImage">
                                        <span class="svg-icon svg-icon-md">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none"
                                                    fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                        fill="#000000" opacity="0.3" />
                                                    <path
                                                        d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                        fill="#000000" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                        Invoice
                                    </label>
                                </a>
                                <a class="input-group col-5" type="button" id="SuratJalanBtn" aria-haspopup="true"
                                    aria-expanded="false">
                                    <label class="input-group-text" for="suratJalan">
                                        <span class="svg-icon svg-icon-md">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none"
                                                    fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                        fill="#000000" opacity="0.3" />
                                                    <path
                                                        d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                        fill="#000000" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                        Surat Jalan
                                    </label>
                                </a>
                                <div class="mt-2">
                                    <a class="input-group col-5" type="button" id="SuratJalanImageBtn"
                                        aria-haspopup="true" aria-expanded="false">
                                        <label class="input-group-text" for="suratJalan">
                                            <span class="svg-icon svg-icon-md">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                            fill="#000000" opacity="0.3" />
                                                        <path
                                                            d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                            fill="#000000" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            Gambar Surat Jalan
                                        </label>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <label>Ongkos Kirim</label>
                            <br />
                            <label> * diisi setelah mengisi kolom terima </label>
                            <input type="number" id="shipping_cost" class="form-control" name="shipping_cost"
                                onchange="updateCogs()" required />
                        </div>
                        <div class="col-4 mt-4">
                            <label class="badge badge-primary">Note</label>
                            <input type="text" id="invoice_note" class="form-control" value="" placeholder="RTO,RTR,INSTOCK"/>
                        </div>
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row mt-2">
                        <div class="col-12" id="purchase_order_detail_content"></div>
                    </div>
                    <!--end::Row-->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark font-weight-bold" id="save_purchase_order_btn">Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="PurchaseOrderReceiveDetailModal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">History Terima <span
                        id="purchase_article_label"></span></h5>
            </div>

            <div class="modal-body table-responsive">
                <input type="hidden" id="poad_id" /><br />
                <table class="table table-hover" id="Poadstb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Tanggal</th>
                            <th class="text-dark">Tipe</th>
                            <th class="text-dark">Pajak</th>
                            <th class="text-dark" style="white-space:nowrap;">Qty Terima</th>
                            <th class="text-dark">Disc</th>
                            <th class="text-dark">ExDisc</th>
                            <th class="text-dark">HPP</th>
                            <th class="text-dark">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="EditPoadsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" style="overflow-y: auto;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_poads" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_poads_id" id="_poads_id" value="" />
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Revisi Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">tanggal</label>
                            <input type="text" class="form-control" id="created_at" name="created_at" readonly />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Tipe Stok</label>
                            <select class="form-control" id="stkt_id_edit" name="stkt_id_edit" required>
                                <option value="">- Pilih -</option>
                                <?php $__currentLoopData = $data['stkt_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Qty Terima</label>
                            <input type="text" class="form-control" id="poads_qty" name="poads_qty" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Discount</label>
                            <input type="text" class="form-control" id="poads_discount" name="poads_discount" />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Extra Discount</label>
                            <input type="text" class="form-control" id="poads_extra_discount"
                                name="poads_extra_discount" />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">HPP</label>
                            <input type="text" class="form-control" id="poads_purchase_price"
                                name="poads_purchase_price" />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Total</label>
                            <input type="text" class="form-control" id="poads_total_price"
                                name="poads_total_price" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <!--<button type="button" class="btn btn-danger font-weight-bold" id="delete_poads_btn" style="display:none;">Hapus</button>-->
                    <button type="submit" class="btn btn-dark font-weight-bold" id="save_poads_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="PoReportModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Laporan Penerimaan</h5>
            </div>

            <div class="modal-body table-responsive">
                <a class="btn-sm btn-primary float-right" id="excel_report">Excel</a><br />
                <table class="table table-hover" id="PoReporttb">
                    <thead class="text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark" style="white-space:nowrap;">Tanggal PO</th>
                            <th class="text-dark" style="white-space:nowrap;">Nomor PO</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Warna</th>
                            <th class="text-dark">Size</th>
                            <th class="text-dark">Order</th>
                            <th class="text-dark">Terima</th>
                            <th class="text-dark">Harga Beli</th>
                            <th class="text-dark">Total Beli</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<form id="f_import" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih template yang sudah diisi data</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="importFile" id="importFile"
                                accept=".csv" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="InvoiceImagesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Invoice Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="container">
                        <table id="purchaseOrderInvoiceImagesTb" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                    data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal-->

<!-- Modal-->
<div class="modal fade" id="SuratJalanImageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Surat Jalan Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="container">
                        <table id="purchaseOrderDeliveryOrderImagesTb" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                    data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal-->

<!-- Modal-->
<form id="f_take_photo" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal fade" id="SuratJalanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Surat Jalan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="my_camera"></div>
                                    <br />
                                    <input type=button value="Take Snapshot" onClick="take_snapshot()">
                                    
                                    <input type="hidden" name="deliveryOrderImage" id="image-tag"
                                        class="image-tag">
                                </div>
                                <div class="col-md-6">
                                    <div id="results">Your captured image will appear here...</div>
                                </div>
                                <div class="col-md-12 text-center">
                                    <br />
                                    
                                    <button type="submit" class="btn btn-dark font-weight-bold" id="take_photo_btn">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal-->
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/purchase_order_receive/purchase_order_receive_modal.blade.php ENDPATH**/ ?>