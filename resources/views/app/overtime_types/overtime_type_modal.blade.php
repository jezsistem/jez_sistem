
<!-- Modal-->
<div class="modal fade" id="ExternalAssignmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_overtime_type">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">External Assignment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Type Name*</label>
                            <input type="text" class="form-control" id="ot_name" name="ot_name" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Description</label>
                            <textarea class="form-control" name="ot_desc" id="ot_desc" cols="10" rows="10"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger font-weight-bold" id="delete_overtime_type_btn"
                        style="display:none;">Hapus</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="save_external_assignment_type_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
