<!-- Modal-->
<div class="modal fade" id="GroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_group">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id_gr" id="_id_gr" value="" />
            <input type="hidden" name="_mode_gr" id="_mode_gr" value="" />
            <input type="hidden" name="_old_item_gr" id="_old_item_gr" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">User Level</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Level *</label>
                        <input type="text" class="form-control" id="gr_name" name="gr_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Deskripsi</label>
                        <input type="text" class="form-control" id="gr_description" name="gr_description"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_group_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_group_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="UserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_user">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Level <span class="text-danger">*</span></label>
                        <select class="form-control" id="gr_id" name="gr_id" required>
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['gr_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="gr_id_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Akses Hapus Data <span class="text-danger">*</span>  </label>
                        <select class="form-control" id="delete_access" name="delete_access" required>
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Store <span class="text-danger">*</span></label>
                        <select class="form-control" id="st_id" name="st_id" required>
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="st_id_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Divisi <span class="text-danger">*</span>  </label>
                        <select class="form-control" id="stt_id" name="stt_id" required>
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['stt_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="stt_id_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="u_name" name="u_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">NIP</label>
                        <input type="text" class="form-control" id="u_nip" name="u_nip" />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">KTP</label>
                        <input type="number" class="form-control" id="u_ktp" name="u_ktp" />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kode</label>
                        <input type="text" class="form-control" id="u_secret_code" name="u_secret_code"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="u_email" name="u_email" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Password</label>
                        <input type="password" class="form-control" id="u_password" name="u_password"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">No Telp <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="u_phone" name="u_phone" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Alamat</label>
                        <input type="text" class="form-control" id="u_address" name="u_address"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_user_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_user_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- =============================================================================================== -->

<div class="modal fade" id="MenuAccessModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Menu Access</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <div class="row justify-content-between">
                <div id="menu_panel"></div>
                <a href="#" class="btn btn-sm btn-primary font-weight-bolder col-3" id="add_menu_access_btn">
                <span class="svg-icon svg-icon-md">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <circle fill="#000000" cx="9" cy="15" r="6" />
                            <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                        </g>
                    </svg>
                </span>Tambah</a>
                <div class="col-12" id="menuList"></div>
                </div><br/><br/>
                <input type="search" class="form-control  col-6" id="menu_search" placeholder="Cari .."/><br/>
                <table class="table table-hover table-checkable" id="MenuAccesstb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Menu <small style="font-size:10px;">* klik nama menu untuk setup default login</small></th>
                            <th class="text-dark"></th>
                            <th class="text-dark"></th>
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
</div><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/user/user_modal.blade.php ENDPATH**/ ?>