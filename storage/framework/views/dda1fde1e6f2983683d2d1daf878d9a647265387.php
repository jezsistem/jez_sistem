<!-- Modal-->
<div class="modal fade" id="ProductLocationSetupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y:auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="f_product_location_setup">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">BIN</h5> <span id="stock_location_label" class="col-6"></span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin::Dropdown-->
                <div class="dropdown dropdown-inline mr-2">
                    <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="svg-icon svg-icon-md">
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
                                <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>Export</button>
                    <!--begin::Dropdown Menu-->
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi flex-column navi-hover py-2">
                            <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Bentuk File :</li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-copy"></i>
                                    </span>
                                    <span id="product_in_location_excel_btn"></span>
                                </a>
                            </li>
                        </ul>
                        <!--end::Navigation-->
                    </div>
                    <!--end::Dropdown Menu-->
                </div>
                <!--end::Dropdown-->
                <!--end::Button--><br/>
                <div class="table-responsive">
                    <input type="search" class="form-control  col-6" id="product_in_location_search" placeholder="Cari artikel / warna / size / brand"/><br/>
                    <table class="table table-hover table-checkable" id="ProductInLocationtb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th style="white-space: nowrap;" class="text-light">Artikel</th>
                                <th style="white-space: nowrap;" class="text-light">Warna</th>
                                <th style="white-space: nowrap;" class="text-light">Size | Qty | Barcode</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ProductMutationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y:auto;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_mutation">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_pl_id_origin" id="_pl_id_origin" value="" />
            <input type="hidden" name="_pst_id" id="_pst_id" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Mutasi [<span id="p_name_mutation"></span>] [<span id="p_color_mutation"></span>] Size [<span id="sz_name_mutation"></span>] Tersedia [<span id="pls_qty_mutation"></span>]</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Jumlah </label>
                        <input type="text" class="form-control" id="mt_qty" name="mt_qty" required />
                    </div><br/>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">BIN Tujuan</label><br/>
                        <select class="form-control" id="pl_id_destination" name="pl_id_destination" required>
                            <option value="">- BIN Tujuan -</option>
                            <?php $__currentLoopData = $data['pl_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="pl_id_destination_parent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="mutation_btn">Mutasi</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AddProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y:auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Setup Artikel <span id="setup_artikel_label"></span></h5>
            </div>
            <div class="modal-body">
                <div class="card-header flex-wrap py-1">
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="br_id_filter_item" name="br_id_filter_item" required>
                                <option value="">- Brand/All -</option>
                                <?php $__currentLoopData = $data['br_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="br_id_filter_parent_item"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="mc_id_filter_item" name="mc_id_filter_item" required>
                                <option value="">- Warna/All -</option>
                                <?php $__currentLoopData = $data['mc_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="mc_id_filter_parent_item"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="sz_id_filter_item" name="sz_id_filter_item" required>
                                <option value="">- Size/All -</option>
                                <?php $__currentLoopData = $data['sz_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="sz_id_filter_parent_item"></div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <!--begin: Datatable-->
                    <center><input type="search" class="form-control  col-6" id="product_search" placeholder="Cari nama produk / warna / brand / supplier"/></center><br/>
                    <table class="table table-hover table-checkable" id="Producttb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th style="white-space: nowrap;" class="text-light">Artikel</th>
                                <th style="white-space: nowrap;" class="text-light">Detail</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="add_product_finish_btn">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AddProductInLocationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y:auto;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_add">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_pst_id_add" id="_pst_id_add" value="" />
            <input type="hidden" name="_unset" id="_unset" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Setup [<span id="p_name_add"></span>] Size [<span id="sz_name_add"></span>] Unset [<span id="sz_unset"></span>]</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Jumlah </label>
                        <input type="text" class="form-control" id="mt_qty_add" name="mt_qty_add" required />
                    </div><br/>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">BIN Tujuan</label><br/>
                        <select class="form-control" id="pl_id_destination_add" name="pl_id_destination_add">
                            <option value="">- BIN Tujuan -</option>
                            <?php $__currentLoopData = $data['pl_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="pl_id_destination_add_parent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="add_btn">Tambah</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/product_location_setup/product_location_setup_modal.blade.php ENDPATH**/ ?>