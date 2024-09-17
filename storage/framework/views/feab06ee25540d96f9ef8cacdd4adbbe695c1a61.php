<!-- Modal-->
<div class="modal fade" id="DataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel"><?php echo e($data['subtitle']); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Store</label>
                        <select class="form-control" id="st_id" name="st_id">
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Divisi</label>
                        <select class="form-control" id="stt_id" name="stt_id">
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['stt_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Penanggung Jawab</label>
                        <select class="form-control" id="u_id" name="u_id">
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['u_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH /home/jezpro.id/public_html/resources/views/app/invoice_editor/invoice_editor_modal.blade.php ENDPATH**/ ?>